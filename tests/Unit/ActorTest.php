<?php

declare(strict_types=1);

namespace Zodimo\Actor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zodimo\Actor\Actor;
use Zodimo\Actor\ActrorRefInterface;
use Zodimo\Actor\Behaviour;
use Zodimo\Actor\EffectInterface;
use Zodimo\Actor\Effects\StayEffect;
use Zodimo\Actor\Mailbox;
use Zodimo\BaseReturnTest\MockClosureTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class ActorTest extends TestCase
{
    use MockClosureTrait;

    public function testCanCreate(): void
    {
        $mailbox = $this->createMock(Mailbox::class);
        $contructor = function (ActrorRefInterface $self) {
            return Behaviour::create(function ($message): EffectInterface {
                return StayEffect::create();
            });
        };
        $actor = Actor::create($mailbox, $contructor);
        $this->assertInstanceOf(ActrorRefInterface::class, $actor);
        $this->assertInstanceOf(Actor::class, $actor);
    }
}
