<?php

declare(strict_types=1);

namespace Zodimo\Actor;

use Zodimo\BaseReturn\IOMonad;

/**
 *  Safe outbox.
 *
 * @template MESSAGE
 * @template OFFERERR
 */
interface Outbox
{
    /**
     * @param MESSAGE $message
     *
     * @return IOMonad<null,OFFERERR>
     */
    public function offer($message): IOMonad;
}
