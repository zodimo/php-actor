<?php

declare(strict_types=1);

namespace Zodimo\Actor\Messages;

/**
 * @template MESSAGE
 *
 * @template-implements MessageInterface<MESSAGE>
 */
class WrappedMessage implements MessageInterface
{
    /**
     * Summary of __construct.
     *
     * @param MESSAGE $message
     */
    private function __construct(private $message) {}

    /**
     * @template _MESSAGE
     *
     * @param _MESSAGE $message
     *
     * @return MessageInterface<_MESSAGE>
     */
    public static function wrap($message): MessageInterface
    {
        return new self($message);
    }

    /**
     * @return MESSAGE
     */
    public function unwrap(): mixed
    {
        return $this->message;
    }
}
