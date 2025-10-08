<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver;

use Boson\Component\CpuInfo\ArchitectureInterface;

interface CoresDriverInterface
{
    /**
     * @return int<1, max>|null
     */
    public function tryGetCores(ArchitectureInterface $arch): ?int;
}
