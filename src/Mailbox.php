<?php

declare(strict_types=1);

namespace Zodimo\Actor;

/**
 *  Safe mailbox.
 *
 * @template MESSAGE
 * @template TAKEERROR
 * @template OFFERERROR
 *
 * @template-extends Inbox<MESSAGE,TAKEERROR>
 * @template-extends Outbox<MESSAGE,OFFERERROR>
 */
interface Mailbox extends Inbox, Outbox {}
