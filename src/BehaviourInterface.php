<?php

declare(strict_types=1);

namespace Zodimo\Actor;

/**
 * @template MESSAGE
 */
interface BehaviourInterface
{
    /**
     * @param MESSAGE $message
     *
     * @return EffectInterface<MESSAGE>
     */
    public function receive($message): EffectInterface;
}
