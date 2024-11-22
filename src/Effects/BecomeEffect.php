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
class BecomeEffect implements EffectInterface
{
    /**
     * @param BehaviourInterface<mixed> $nextBehaviour
     */
    private function __construct(private BehaviourInterface $nextBehaviour) {}

    /**
     * @param BehaviourInterface<mixed> $nextBehaviour
     *
     * @return BecomeEffect<mixed>
     */
    public static function create(BehaviourInterface $nextBehaviour): BecomeEffect
    {
        return new self($nextBehaviour);
    }

    /**
     * @template _MESSAGE
     *
     * @param BehaviourInterface<_MESSAGE> $_
     *
     * @return BehaviourInterface<_MESSAGE>
     */
    public function transition(BehaviourInterface $_): BehaviourInterface
    {
        return $this->nextBehaviour;
    }
}
