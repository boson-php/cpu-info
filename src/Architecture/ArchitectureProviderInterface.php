<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture;

use Boson\Component\CpuInfo\ArchitectureInterface;

interface ArchitectureProviderInterface
{
    /**
     * Gets current CPU architecture type
     */
    public ArchitectureInterface $arch { get; }
}
