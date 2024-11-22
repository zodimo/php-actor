<?php

declare(strict_types=1);

namespace Zodimo\Actor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zodimo\Actor\ActorSystem;
use Zodimo\Actor\Runtimes\ExecutorService;
use Zodimo\Actor\Runtimes\MailboxFactory;

/**
 * @internal
 *
 * @coversNothing
 */
class ActorSystemTest extends TestCase
{
    public function testCreate(): void
    {
        $executorService = $this->createMock(ExecutorService::class);
        $mailboxFactory = $this->createMock(MailboxFactory::class);
        $actorSystem = ActorSystem::create($executorService, $mailboxFactory);
        $this->assertInstanceOf(ActorSystem::class, $actorSystem);
    }
}
