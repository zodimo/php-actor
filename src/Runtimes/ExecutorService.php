<?php

declare(strict_types=1);

namespace Zodimo\Actor\Runtimes;

use Zodimo\Actor\Actor;

interface ExecutorService
{
    /**
     * @param Actor<mixed> $actor
     */
    public function execute(Actor $actor): void;
}
