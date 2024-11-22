<?php

declare(strict_types=1);

namespace Zodimo\Actor\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Zodimo\Actor\ActorSystem;
use Zodimo\Actor\Runtimes\DiscreteTime\DiscreteExecutrorService;
use Zodimo\Actor\Runtimes\DiscreteTime\DiscreteMailboxFactory;
use Zodimo\Actor\Runtimes\DiscreteTime\ExecutionStepper;
use Zodimo\FRP\RootSignalInterface;
use Zodimo\FRP\SignalService;
use Zodimo\FRPTesting\FrpTestingEnvironmentFactoryTrait;

/**
 * @internal
 *
 * @coversNothing
 */
class ActorSystemTest extends TestCase
{
    use FrpTestingEnvironmentFactoryTrait;

    public SignalService $signalService;

    public function setUp(): void
    {
        $env = $this->createFrpTestEnvironment();
        $this->signalService = $env->container->get(SignalService::class);
    }

    public function getSignalService(): SignalService
    {
        return $this->signalService;
    }

    /**
     * @return RootSignalInterface<int,mixed>
     */
    public function getTimeStepSignal(int $step = 0): RootSignalInterface
    {
        // @phpstan-ignore argument.templateType
        return $this->getSignalService()->createRootSignal($step);
    }

    public function testSetup(): void
    {
        $timeStepper = ExecutionStepper::create($this->getTimeStepSignal());
        $executorService = DiscreteExecutrorService::create($timeStepper);

        $mailboxFactory = new DiscreteMailboxFactory($timeStepper);
        $actorSystem = ActorSystem::create($executorService, $mailboxFactory);
        $this->assertInstanceOf(ActorSystem::class, $actorSystem);
    }

    public function testSignal(): void
    {
        $timeStepper = ExecutionStepper::create($this->getTimeStepSignal());
        $this->assertEquals(0, $timeStepper->getCurrentStep());
        $timeStepper->tick();
        $this->assertEquals(1, $timeStepper->getCurrentStep());
    }

    public function testMailboxEffect(): void
    {
        // tick - > mailbox processor...

        // $reactiveMailbox -> on tick -> and no empty, run actor until complete...
        $this->assertTrue(true);
    }
}
