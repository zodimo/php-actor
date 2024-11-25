<?php

declare(strict_types=1);

namespace Zodimo\Actor\Messages;

use Zodimo\Actor\ActrorRefInterface;

/**
 * @template MESSAGE
 *
 * @template-implements MessageInterface<MESSAGE>
 */
class AddressMessage implements MessageInterface
{
    /**
     * @param ActrorRefInterface<MESSAGE> $address
     */
    private function __construct(private ActrorRefInterface $address) {}

    /**
     * @template _MESSAGE
     *
     * @param ActrorRefInterface<_MESSAGE> $address
     *
     * @return AddressMessage<_MESSAGE>
     */
    public static function create(ActrorRefInterface $address): AddressMessage
    {
        return new self($address);
    }

    /**
     * @return ActrorRefInterface<MESSAGE>
     */
    public function getAddress(): ActrorRefInterface
    {
        return $this->address;
    }
}
