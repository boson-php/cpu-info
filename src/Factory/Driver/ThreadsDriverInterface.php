<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver;

use Boson\Component\CpuInfo\ArchitectureInterface;

interface ThreadsDriverInterface
{
    /**
     * @return int<1, max>|null
     */
    public function tryGetThreads(ArchitectureInterface $arch): ?int;
}
