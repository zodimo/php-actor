<?php

declare(strict_types=1);

namespace Zodimo\Actor\Runtimes;

use Zodimo\Actor\Address;

interface ExecutorService
{
    /**
     * @param Address<mixed> $address
     */
    public function execute(Address $address): void;
}
