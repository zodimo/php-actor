<?php

declare(strict_types=1);

namespace Zodimo\Actor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zodimo\Actor\Address;
use Zodimo\Actor\AddressInterface;
use Zodimo\Actor\Behaviour;
use Zodimo\Actor\EffectInterface;
use Zodimo\Actor\Effects\DieEffect;
use Zodimo\Actor\Effects\StayEffect;
use Zodimo\Actor\Mailbox;
use Zodimo\BaseReturn\IOMonad;
use Zodimo\BaseReturn\Option;
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
        $mailbox = $this->createMock(Mailbox::class);
        $contructor = function (AddressInterface $self) {
            return Behaviour::create(function ($message): EffectInterface {
                return StayEffect::create();
            });
        };
        $address = Address::create($mailbox, $contructor);
        $this->assertInstanceOf(Address::class, $address);
    }

    public function testCanReceiveMessage(): void
    {
        $mailbox = $this->createMock(Mailbox::class);
        $mailbox->expects($this->once())->method('offer')->with('hello')->willReturn(IOMonad::pure(null));
        $contructor = function (AddressInterface $self) {
            return Behaviour::create(function ($message): EffectInterface {
                return StayEffect::create();
            });
        };
        $address = Address::create($mailbox, $contructor);
        $address->tell('hello');
    }

    public function testCanProcessMessageWhenRun(): void
    {
        $mailbox = $this->createMock(Mailbox::class);
        $mailbox->expects($this->exactly(2))->method('take')->willReturn(IOMonad::pure(Option::some('hello')), IOMonad::pure(Option::none()));
        $handleMessageClosure = $this->createClosureMock();
        $handleMessageClosure->expects($this->once())->method('__invoke')->with('hello');
        $contructor = function (AddressInterface $self) use ($handleMessageClosure) {
            return Behaviour::create(function ($message) use ($handleMessageClosure): EffectInterface {
                $handleMessageClosure($message);

                return StayEffect::create();
            });
        };
        $address = Address::create($mailbox, $contructor);
        $result = $address->run();
        $this->assertTrue($result->isSuccess());
    }

    public function testCanChangeBehaviourOnMessageToDie(): void
    {
        $mailbox = $this->createMock(Mailbox::class);
        $mailbox->expects($this->exactly(3))->method('take')->willReturn(IOMonad::pure(Option::some('hello')), IOMonad::pure(Option::some('world')), IOMonad::pure(Option::none()));
        $handleMessageClosure = $this->createClosureMock();
        $handleMessageClosure->expects($this->once())->method('__invoke')->with('hello');

        $dieActionClosure = $this->createClosureMock();
        $dieActionClosure->expects($this->once())->method('__invoke')->with('world');

        $contructor = function (AddressInterface $self) use ($handleMessageClosure, $dieActionClosure) {
            return Behaviour::create(function ($message) use ($handleMessageClosure, $dieActionClosure): EffectInterface {
                $handleMessageClosure($message);

                return DieEffect::create($dieActionClosure);
            });
        };
        $address = Address::create($mailbox, $contructor);
        $result = $address->run();
        $this->assertTrue($result->isSuccess());
    }
}
