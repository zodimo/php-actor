<?php

declare(strict_types=1);

namespace Zodimo\Actor\Effects;

use Zodimo\Actor\BehaviourInterface;
use Zodimo\Actor\EffectInterface;

/**
 * @template MESSAGE
 *
 * @template-implements EffectInterface<MESSAGE>
 */
class StayEffect implements EffectInterface
{
    private function __construct() {}

    /**
     * @return StayEffect<mixed>
     */
    public static function create(): StayEffect
    {
        return new self();
    }

    /**
     * @template _MESSAGE
     *
     * @param BehaviourInterface<_MESSAGE> $behaviour
     *
     * @return BehaviourInterface<_MESSAGE>
     */
    public function transition(BehaviourInterface $behaviour): BehaviourInterface
    {
        return $behaviour;
    }
}
