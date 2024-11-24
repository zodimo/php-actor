<?php

declare(strict_types=1);

namespace Zodimo\Actor\Messages;

use Zodimo\Actor\AddressInterface;

/**
 * @template MESSAGE
 *
 * @template-implements MessageInterface<MESSAGE>
 */
class AddressMessage implements MessageInterface
{
    /**
     * @param AddressInterface<MESSAGE> $address
     */
    private function __construct(private AddressInterface $address) {}

    /**
     * @template _MESSAGE
     *
     * @param AddressInterface<_MESSAGE> $address
     *
     * @return AddressMessage<_MESSAGE>
     */
    public static function create(AddressInterface $address): AddressMessage
    {
        return new self($address);
    }

    /**
     * @return AddressInterface<MESSAGE>
     */
    public function getAddress(): AddressInterface
    {
        return $this->address;
    }
}
