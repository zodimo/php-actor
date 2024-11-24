<?php

declare(strict_types=1);

namespace Zodimo\Actor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zodimo\Actor\Address;
use Zodimo\BaseReturnTest\MockClosureTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class AddressTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreate(): void
    {
        $actorClient = $this->createClosureMock();
        $address = Address::create($actorClient);
        $this->assertInstanceOf(Address::class, $address);
    }
}
