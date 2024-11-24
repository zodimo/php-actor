<?php

declare(strict_types=1);
use Zodimo\Actor\ActorSystem;
use Zodimo\Actor\AddressInterface;
use Zodimo\Actor\Behaviour;
use Zodimo\Actor\EffectInterface;
use Zodimo\Actor\Effects\StayEffect;
use Zodimo\Actor\Runtimes\DiscreteTime\DiscreteExecutrorService;
use Zodimo\Actor\Runtimes\DiscreteTime\DiscreteMailboxFactory;
use Zodimo\Actor\Runtimes\DiscreteTime\ExecutionStepper;
use Zodimo\FRP\SignalService;
use Zodimo\FRPTesting\FrpTestingEnvironment;

require_once __DIR__.'/../vendor/autoload.php';

interface Message
{
    /**
     * @return AddressInterface<Message>
     */
    public function getSender(): AddressInterface;
}

class Ping implements Message
{
    /**
     * @param AddressInterface<Message> $sender
     */
    public function __construct(private AddressInterface $sender) {}

    /**
     * @return AddressInterface<Message>
     */
    public function getSender(): AddressInterface
    {
        return $this->sender;
    }
}
class Pong implements Message
{
    /**
     * @param AddressInterface<Message> $sender
     */
    public function __construct(private AddressInterface $sender) {}

    /**
     * @return AddressInterface<Message>
     */
    public function getSender(): AddressInterface
    {
        return $this->sender;
    }
}

// //////////
// SETUP SIGNAL ENVIRONMENT
// //////////

$env = FrpTestingEnvironment::create();

$signalService = $env->container->get(SignalService::class);

$timeStepSignal = $signalService->createRootSignal(0);
$timeStepper = ExecutionStepper::create($timeStepSignal);

$discreteExecutorService = new DiscreteExecutrorService($timeStepper);
$mailboxFactory = DiscreteMailboxFactory::create($timeStepper);

$actorSystem = new ActorSystem($discreteExecutorService, $mailboxFactory);

$actorPingReceiver = $actorSystem->actorOf(Message::class, function (AddressInterface $self) {
    return Behaviour::create(function (Message $message) use ($self): EffectInterface {
        // Do the magic here...
        switch (true) {
            case $message instanceof Ping:
                echo "PING\n";
                $message->getSender()->tell(new Pong($self));

                break;

            default:
                echo "miss\n";
        }

        return StayEffect::create();
    });
});

$actorPongReceiver = $actorSystem->actorOf(Message::class, function (AddressInterface $self) {
    return Behaviour::create(function (Message $message) use ($self): EffectInterface {
        // Do the magic here...
        switch (true) {
            case $message instanceof Pong:
                echo "PONG\n";
                $message->getSender()->tell(new Ping($self));

                break;

            default:
                echo "miss\n";
        }

        return StayEffect::create();
    });
});

$actorPingReceiver->tell(new Ping($actorPongReceiver));

$discreteExecutorService->run(fn ($step) => 100 > $step);
