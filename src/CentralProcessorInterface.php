<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

use Boson\Component\CpuInfo\Architecture\ArchitectureProviderInterface;
use Boson\Component\CpuInfo\InstructionSet\InstructionSetProviderInterface;
use Boson\Component\CpuInfo\Vendor\VendorInfoInterface;

interface CentralProcessorInterface extends
    ArchitectureProviderInterface,
    InstructionSetProviderInterface,
    VendorInfoInterface {}
