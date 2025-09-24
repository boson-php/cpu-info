<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver;

use Boson\Component\CpuInfo\ArchitectureInterface;

interface NameDriverInterface
{
    /**
     * @return non-empty-string|null
     */
    public function tryGetName(ArchitectureInterface $arch): ?string;
}
