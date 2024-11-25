<?php

declare(strict_types=1);

namespace Zodimo\Actor;

/**
 * Should an address be typed ?
 * Type of actor it points to ?
 *
 * AKA ActorRef in apache pekko
 *
 * @template MESSAGE
 */
interface ActrorRefInterface
{
    /**
     * @param MESSAGE $message
     */
    public function tell($message): void;
}
