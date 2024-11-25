<?php

declare(strict_types=1);

namespace Zodimo\Actor;

/**
 * @template MESSAGE
 *
 * @template-implements ActrorRefInterface<MESSAGE>
 */
class ActorRef implements ActrorRefInterface
{
    /**
     * @param callable(MESSAGE):void $actorClient
     */
    private function __construct(private $actorClient) {}

    /**
     * @template _MESSAGE
     *
     * @param callable(_MESSAGE):void $actorClient
     *
     * @return ActrorRefInterface<_MESSAGE>
     */
    public static function create(callable $actorClient): ActrorRefInterface
    {
        return new self($actorClient);
    }

    /**
     * @param MESSAGE $message
     */
    public function tell($message): void
    {
        $client = $this->actorClient;
        $client($message);
    }
}
