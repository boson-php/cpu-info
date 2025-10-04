<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Vendor\VendorInfoInterface;

interface VendorFactoryInterface
{
    public function createVendor(): VendorInfoInterface;
}
