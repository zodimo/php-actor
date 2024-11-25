<?php

declare(strict_types=1);

namespace Zodimo\Actor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zodimo\Actor\ActorRef;
use Zodimo\BaseReturnTest\MockClosureTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class ActorRefTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreate(): void
    {
        $actorClient = $this->createClosureMock();
        $ref = ActorRef::create($actorClient);
        $this->assertInstanceOf(ActorRef::class, $ref);
    }
}
