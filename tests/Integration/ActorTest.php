<?php

declare(strict_types=1);

namespace Zodimo\Actor\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Zodimo\Actor\Actor;
use Zodimo\Actor\Address;
use Zodimo\Actor\AddressInterface;
use Zodimo\Actor\Behaviour;
use Zodimo\Actor\EffectInterface;
use Zodimo\Actor\Effects\DieEffect;
use Zodimo\Actor\Effects\StayEffect;
use Zodimo\Actor\Mailbox;
use Zodimo\Actor\Messages\AddressMessage;
use Zodimo\Actor\Messages\MessageInterface;
use Zodimo\Actor\Messages\WrappedMessage;
use Zodimo\Actor\Runtimes\DiscreteTime\DiscreteExecutrorService;
use Zodimo\Actor\Runtimes\DiscreteTime\DiscreteMailboxFactory;
use Zodimo\Actor\Runtimes\DiscreteTime\ExecutionStepper;
use Zodimo\Actor\Runtimes\MailboxFactory;
use Zodimo\BaseReturnTest\MockClosureTrait;
use Zodimo\FRP\SignalService;
use Zodimo\FRPTesting\FrpTestingEnvironmentFactoryTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class ActorTest extends TestCase
{
    use MockClosureTrait;
    use FrpTestingEnvironmentFactoryTrait;

    public SignalService $signalService;
    public ExecutionStepper $executorStepper;
    public DiscreteExecutrorService $executorService;

    public function setUp(): void
    {
        $env = $this->createFrpTestEnvironment();
        $this->signalService = $env->container->get(SignalService::class);
        $this->executorStepper = ExecutionStepper::create($this->signalService->createRootSignal(0));
        $this->executorService = DiscreteExecutrorService::create($this->executorStepper);
    }

    public function getExecutorService(): DiscreteExecutrorService
    {
        return $this->executorService;
    }

    public function getExecutionStepper(): ExecutionStepper
    {
        return $this->executorStepper;
    }

    /**
     * @return MailboxFactory<mixed,mixed>
     */
    public function getMailboxFactory(): MailboxFactory
    {
        return new DiscreteMailboxFactory($this->getExecutionStepper());
    }

    public function testCanCreate(): void
    {
        $mailbox = $this->createMock(Mailbox::class);
        $contructor = function (AddressInterface $self) {
            return Behaviour::create(function ($message): EffectInterface {
                return StayEffect::create();
            });
        };
        $actor = Actor::create($mailbox, $contructor);
        $this->assertInstanceOf(AddressInterface::class, $actor);
        $this->assertInstanceOf(Actor::class, $actor);
    }

    public function testCanOnlyRecieveAddressAsFirstMessage(): void
    {
        $mailbox = $this->getMailboxFactory()->createMailbox(\stdClass::class);

        $behaviourClosure = $this->createClosureNotCalled();
        $contructor = function (AddressInterface $self) use ($behaviourClosure) {
            return Behaviour::create(function ($message) use ($behaviourClosure): EffectInterface {
                $behaviourClosure();

                return StayEffect::create();
            });
        };
        $actor = Actor::create($mailbox, $contructor);
        $message = new \stdClass();
        $message->message = 'hello';
        $actor->tell($message);
        $this->getExecutorService()->execute($actor);
        $this->getExecutorService()->run(fn ($step) => $step < 100);
    }

    public function testCanReceiveMessageAfterAddress(): void
    {
        $mailbox = $this->getMailboxFactory()->createMailbox(MessageInterface::class);
        $message = WrappedMessage::wrap('hello');

        $behaviourClosure = $this->createClosureMock();
        $behaviourClosure->expects($this->once())->method('__invoke')->with($message);
        $contructor = function (AddressInterface $self) use ($behaviourClosure) {
            return Behaviour::create(function ($message) use ($behaviourClosure): EffectInterface {
                $behaviourClosure($message);

                return StayEffect::create();
            });
        };

        $actor = Actor::create($mailbox, $contructor);

        $addressMessage = AddressMessage::create(Address::create(fn ($message) => $actor->tell($message)));
        $actor->tell($addressMessage);
        $actor->tell($message);
        $this->getExecutorService()->execute($actor);

        $this->getExecutorService()->run(fn ($step) => $step < 100);
    }

    public function testCanChangeBehaviourOnMessageToDie(): void
    {
        $message1 = WrappedMessage::wrap('hello');
        $message2 = WrappedMessage::wrap('world');

        $mailbox = $this->getMailboxFactory()->createMailbox(MessageInterface::class);

        $handleMessageClosure = $this->createClosureMock();
        $handleMessageClosure->expects($this->once())->method('__invoke')->with($message1);

        $dieActionClosure = $this->createClosureMock();
        $dieActionClosure->expects($this->once())->method('__invoke')->with($message2);

        $contructor = function (AddressInterface $self) use ($handleMessageClosure, $dieActionClosure) {
            return Behaviour::create(function ($message) use ($handleMessageClosure, $dieActionClosure): EffectInterface {
                $handleMessageClosure($message);

                return DieEffect::create($dieActionClosure);
            });
        };

        $actor = Actor::create($mailbox, $contructor);
        $addressMessage = AddressMessage::create(Address::create(fn ($message) => $actor->tell($message)));
        $actor->tell($addressMessage);
        $actor->tell($message1);
        $actor->tell($message2);
        $this->getExecutorService()->execute($actor);
        $this->getExecutorService()->run(fn ($step) => $step < 100);
    }
}
