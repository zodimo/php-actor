<?php

declare(strict_types=1);

namespace Zodimo\Actor;

/**
 * `THE` Actor System.
 */
interface ActorSystemInterface
{
    /**
     * @template _MESSAGE
     *
     * @param class-string<_MESSAGE>                                  $messageClass
     * @param callable(ActrorRefInterface<mixed>):Behaviour<_MESSAGE> $constructor
     *
     * @return ActrorRefInterface<_MESSAGE>
     */
    public function actorOf(string $messageClass, callable $constructor): ActrorRefInterface;
}
