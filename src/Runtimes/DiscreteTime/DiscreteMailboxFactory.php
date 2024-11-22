<?php

declare(strict_types=1);

namespace Zodimo\Actor\Runtimes\DiscreteTime;

use Zodimo\Actor\Mailbox;
use Zodimo\Actor\Runtimes\MailboxFactory;

/**
 * @template TAKEERROR
 * @template OFFERERROR
 *
 * @template-implements MailboxFactory<TAKEERROR,OFFERERROR>
 */
class DiscreteMailboxFactory implements MailboxFactory
{
    public function __construct(private ExecutionStepper $execStepper) {}

    /**
     * @return DiscreteMailboxFactory<mixed,mixed>
     */
    public static function create(ExecutionStepper $execStepper): DiscreteMailboxFactory
    {
        return new self($execStepper);
    }

    /**
     * @template MESSAGE
     *
     * @param class-string<MESSAGE> $messageClass
     *
     * @return Mailbox<MESSAGE,TAKEERROR,OFFERERROR>
     */
    public function createMailbox(string $messageClass): Mailbox
    {
        return DiscreteMailbox::create($this->execStepper, $messageClass);
    }
}
