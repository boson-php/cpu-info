<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor;

interface VendorInfoInterface
{
    /**
     * Gets current CPU name
     *
     * @var non-empty-string
     */
    public string $name { get; }

    /**
     * Gets current CPU generic vendor name
     *
     * @var non-empty-string|null
     */
    public ?string $vendor { get; }

    /**
     * Gets the number of physical CPU cores.
     *
     * @var int<1, max>
     */
    public int $physicalCores { get; }

    /**
     * Gets the number of logical CPU cores.
     *
     * Note: The number of logical cores can be equal to or greater
     *       than the number of physical cores ({@see $physicalCores}).
     *
     * @var int<1, max>
     */
    public int $logicalCores { get; }
}
