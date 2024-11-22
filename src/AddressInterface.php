<?php

declare(strict_types=1);

namespace Zodimo\Actor;

/**
 * @template MESSAGE
 */
interface AddressInterface
{
    /**
     * @param MESSAGE $message
     */
    public function tell($message): void;
}
