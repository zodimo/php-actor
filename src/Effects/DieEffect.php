<?php

declare(strict_types=1);

namespace Zodimo\Actor\Effects;

use Zodimo\Actor\Behaviour;
use Zodimo\Actor\BehaviourInterface;
use Zodimo\Actor\EffectInterface;
use Zodimo\BaseReturn\Option;

/**
 * @template MESSAGE
 *
 * @template-implements EffectInterface<MESSAGE>
 */
class DieEffect implements EffectInterface
{
    /**
     * @param Option<callable(MESSAGE):void> $action
     */
    private function __construct(private Option $action) {}

    /**
     * @param ?callable(MESSAGE):void $action
     *
     * @return DieEffect<mixed>
     */
    public static function create(?callable $action = null): DieEffect
    {
        if (null !== $action) {
            $actionOption = Option::some($action);
        } else {
            $actionOption = Option::none();
        }

        return new self($actionOption);
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
        $actionOption = $this->action;
        $finalBehaviour = Behaviour::create(function ($message) use ($actionOption): StayEffect {
            $actionOption->match(
                fn ($callable) => $callable($message),
                fn () => null
            );

            return StayEffect::create();
        });

        return BecomeEffect::create($finalBehaviour)->transition($behaviour);
    }
}
