<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver;

use Boson\Component\CpuInfo\ArchitectureInterface;

/**
 * Returns general (and imprecise) CPU information
 */
final readonly class GenericDriver implements VendorDriverInterface
{
    /**
     * @return non-empty-string
     */
    public function tryGetVendor(ArchitectureInterface $arch): string
    {
        /** @var non-empty-string */
        return \php_uname('m');
    }
}
