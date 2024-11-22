<?php

declare(strict_types=1);

namespace Zodimo\Actor;

use Zodimo\BaseReturn\IOMonad;
use Zodimo\BaseReturn\Option;

/**
 * Safe inbox.
 *
 * @template MESSAGE
 * @template TAKEERR
 */
interface Inbox
{
    /**
     * @return IOMonad<Option<MESSAGE>,TAKEERR>
     */
    public function take(): IOMonad;
}
