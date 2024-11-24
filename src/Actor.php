<?php

declare(strict_types=1);

namespace Zodimo\Actor;

use Zodimo\Actor\Effects\BecomeEffect;
use Zodimo\Actor\Effects\StayEffect;
use Zodimo\Actor\Messages\AddressMessage;
use Zodimo\BaseReturn\IOMonad;
use Zodimo\BaseReturn\Option;

/**
 * @template MESSAGE
 *
 * @template-implements AddressInterface<MESSAGE>
 */
class Actor implements AddressInterface
{
    /**
     * @var AddressInterface<MESSAGE>
     */
    private AddressInterface $ownAddress;

    /**
     * @var BehaviourInterface<MESSAGE>
     */
    private BehaviourInterface $behaviour;

    /**
     * @param Mailbox<MESSAGE,mixed,mixed> $mailbox
     */
    private function __construct(private Mailbox $mailbox, callable $constructor)
    {
        $that = $this;

        $addressHandleBehaviour = Behaviour::create(function ($message) use ($that, $constructor) {
            if (is_object($message) and $message instanceof AddressMessage) {
                $that->ownAddress = $message->getAddress();

                return BecomeEffect::create($constructor($that->ownAddress));
            }
            // log or throw  ?
            // expected first message must be address.... ???

            return StayEffect::create();
        });

        $this->behaviour = $addressHandleBehaviour;
    }

    /**
     * @template _MESSAGE
     *
     * @param Mailbox<_MESSAGE,mixed,mixed> $mailbox
     *
     * @return Actor<_MESSAGE>|Actor<AddressMessage<_MESSAGE>>
     */
    public static function create(Mailbox $mailbox, callable $constructor): Actor
    {
        return new self($mailbox, $constructor);
    }

    /**
     * @param MESSAGE $message
     */
    public function tell($message): void
    {
        $this->mailbox->offer($message);
    }

    // /////////////

    /**
     * Runnable Address.
     * process all the messages until the mailbox is empty or an error occurred.
     *
     * @return IOMonad<null,mixed>
     */
    public function run(): IOMonad
    {
        // recursive run... or loop ?
        $that = $this;

        $loopResult = IOMonad::pure(true);
        while ($loopResult->isSuccess()) {
            $loopAgain = $loopResult->match(
                fn ($continue) => $continue,
                fn ($_) => false,
            );
            if ($loopAgain) {
                // mailbox->take return option<message>, none==empty
                $loopResult = $this->mailbox->take()->match(
                    fn ($messageOption) => $messageOption->match(
                        function ($message) use ($that) {
                            $that->receive($message);

                            return IOMonad::pure(true);
                        },
                        fn () => IOMonad::pure(false)// mailbox is empty
                    ),
                    fn ($error) => IOMonad::fail(throw new \RuntimeException('Got and error taking a message from the mailbox'))
                );
            } else {
                break;
            }
        }

        return $loopResult->fmap(fn ($_) => null);
    }

    /**
     * Receive message and transition ;).
     *
     * @param MESSAGE $message
     */
    private function receive($message): void
    {
        $behaviour = $this->behaviour;
        $this->behaviour = $behaviour->receive($message)->transition($behaviour);
    }
}
