<?php

declare(strict_types=1);

namespace Zodimo\Actor\Runtimes;

use Zodimo\Actor\Mailbox;

/**
 * @template TAKEERROR
 * @template OFFERERROR
 */
interface MailboxFactory
{
    /**
     * @template MESSAGE
     *
     * @param class-string<MESSAGE> $messageClass
     *
     * @return Mailbox<MESSAGE,TAKEERROR,OFFERERROR>
     */
    public function createMailbox(string $messageClass): Mailbox;
}
