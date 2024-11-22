<?php

declare(strict_types=1);

namespace Zodimo\Actor\Runtimes\DiscreteTime;

use Zodimo\FRP\RootSignalInterface;

class ExecutionStepper
{
    /**
     * @param RootSignalInterface<int,mixed> $step
     */
    private function __construct(private RootSignalInterface $step) {}

    /**
     * @param RootSignalInterface<int,mixed> $step
     */
    public static function create(RootSignalInterface $step): ExecutionStepper
    {
        return new self($step);
    }

    public function tick(): void
    {
        $this->step->setValue($this->step->getValue() + 1);
    }

    public function getCurrentStep(): int
    {
        return $this->step->getValue();
    }
}
