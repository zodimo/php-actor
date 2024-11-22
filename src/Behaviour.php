<?php

declare(strict_types=1);

namespace Zodimo\Actor;

/**
 * @template MESSAGE
 *
 * @template-implements BehaviourInterface<MESSAGE>
 */
class Behaviour implements BehaviourInterface
{
    /**
     * @param callable(MESSAGE):EffectInterface<MESSAGE> $behaviourFunc
     */
    private function __construct(private $behaviourFunc) {}

    /**
     * @template _MESSAGE
     *
     * @param callable(_MESSAGE):EffectInterface<_MESSAGE> $behaviourFunc
     *
     * @return Behaviour<_MESSAGE>
     */
    public static function create(callable $behaviourFunc): Behaviour
    {
        return new Behaviour($behaviourFunc);
    }

    /**
     * @param MESSAGE $message
     *
     * @return EffectInterface<MESSAGE>
     */
    public function receive($message): EffectInterface
    {
        $func = $this->behaviourFunc;

        return $func($message);
    }
}
