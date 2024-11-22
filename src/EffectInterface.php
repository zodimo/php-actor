<?php

declare(strict_types=1);

namespace Zodimo\Actor;

/**
 * @template MESSAGE
 */
interface EffectInterface
{
    /**
     * @param BehaviourInterface<MESSAGE> $behaviour
     *
     * @return BehaviourInterface<MESSAGE>
     */
    public function transition(BehaviourInterface $behaviour): BehaviourInterface;
}
