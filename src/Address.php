<?php

declare(strict_types=1);

namespace Zodimo\Actor;

use Zodimo\BaseReturn\IOMonad;

/**
 * @template MESSAGE
 *
 * @template-implements AddressInterface<MESSAGE>
 */
class Address implements AddressInterface
{
    /**
     * @var BehaviourInterface<MESSAGE>
     */
    private BehaviourInterface $behaviour;

    /**
     * @param Mailbox<MESSAGE,mixed,mixed> $mailbox
     */
    public function __construct(private Mailbox $mailbox, callable $constructor)
    {
        $this->behaviour = $constructor($this);
    }

    /**
     * @template _MESSAGE
     *
     * @param Mailbox<_MESSAGE,mixed,mixed> $mailbox
     *
     * @return Address<_MESSAGE>
     */
    public static function create(Mailbox $mailbox, callable $constructor): Address
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

    /**
     * Runnable Address.
     * process all the messages until the mailbox is empty or an error occurred.
     *
     * @return IOMonad<null,mixed>
     */
    public function run(): IOMonad
    {
        // recursive run...
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
