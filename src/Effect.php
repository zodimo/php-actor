<?php

declare(strict_types=1);

namespace Zodimo\Actor;

/**
 * Effect is just a transition mapping.
 *
 * @template MESSAGE
 *
 * @template-implements EffectInterface<MESSAGE>
 */
class Effect implements EffectInterface
{
    /**
     * @param callable(BehaviourInterface<MESSAGE>):BehaviourInterface<MESSAGE> $transition
     */
    private function __construct(private $transition) {}

    /**
     * @param BehaviourInterface<MESSAGE> $behaviour
     *
     * @return BehaviourInterface<MESSAGE>
     */
    public function transition(BehaviourInterface $behaviour): BehaviourInterface
    {
        $transition = $this->transition;

        return $transition($behaviour);
    }

    /**
     * @template _MESSAGE
     *
     * @param callable(BehaviourInterface<_MESSAGE>):BehaviourInterface<_MESSAGE> $transition
     *
     * @return EffectInterface<_MESSAGE>
     */
    public static function create(callable $transition): EffectInterface
    {
        return new Effect($transition);
    }
}
