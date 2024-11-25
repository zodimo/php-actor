<?php

declare(strict_types=1);
use Zodimo\Actor\ActorSystem;
use Zodimo\Actor\ActrorRefInterface;
use Zodimo\Actor\Behaviour;
use Zodimo\Actor\EffectInterface;
use Zodimo\Actor\Effects\DieEffect;
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
     * @return ActrorRefInterface<Message>
     */
    public function getSender(): ActrorRefInterface;
}

class Ping implements Message
{
    /**
     * @param ActrorRefInterface<Message> $sender
     */
    public function __construct(private ActrorRefInterface $sender, private int $sequenceNumber) {}

    /**
     * @return ActrorRefInterface<Message>
     */
    public function getSender(): ActrorRefInterface
    {
        return $this->sender;
    }

    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
    }
}
class Pong implements Message
{
    /**
     * @param ActrorRefInterface<Message> $sender
     */
    public function __construct(private ActrorRefInterface $sender, private int $sequenceNumber) {}

    /**
     * @return ActrorRefInterface<Message>
     */
    public function getSender(): ActrorRefInterface
    {
        return $this->sender;
    }

    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
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

$pingCountLimit = 20;

$actorPingReceiver = $actorSystem->actorOf(Message::class, function (ActrorRefInterface $self) use ($pingCountLimit) {
    return Behaviour::create(function (Message $message) use ($self, $pingCountLimit): EffectInterface {
        // Do the magic here...
        switch (true) {
            case $message instanceof Ping:
                $seq = $message->getSequenceNumber();

                if ($seq > $pingCountLimit) {
                    return DieEffect::create();
                }

                echo "PING: seq {$seq}\n";
                $message->getSender()->tell(new Pong($self, $seq));

                break;

            default:
                echo "miss\n";
        }

        return StayEffect::create();
    });
});

$actorPongReceiver = $actorSystem->actorOf(Message::class, function (ActrorRefInterface $self) {
    return Behaviour::create(function (Message $message) use ($self): EffectInterface {
        // Do the magic here...
        switch (true) {
            case $message instanceof Pong:
                $seq = $message->getSequenceNumber();
                echo "PONG: seq {$seq}\n";
                $message->getSender()->tell(new Ping($self, $seq + 1));

                break;

            default:
                echo "miss\n";
        }

        return StayEffect::create();
    });
});

$actorPingReceiver->tell(new Ping($actorPongReceiver, 0));

$discreteExecutorService->run(fn ($step) => 100 > $step);
