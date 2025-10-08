<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\Factory\Driver\WindowsSysInfo\Kernel32;
use FFI\CData;
use FFI\Env\Runtime;

final class WindowsSysInfoDriver implements
    ThreadsDriverInterface,
    CoresDriverInterface
{
    private const int RELATION_PROCESSOR_CORE = 0x00;

    private Kernel32 $kernel32 {
        get => $this->kernel32 ??= new Kernel32();
    }

    public function tryGetThreads(ArchitectureInterface $arch): ?int
    {
        if (\PHP_OS_FAMILY !== 'Windows' || !Runtime::isAvailable()) {
            return null;
        }

        $info = $this->kernel32->new('SYSTEM_INFO');

        $this->kernel32->GetNativeSystemInfo(\FFI::addr($info));

        if ($info->dwNumberOfProcessors === 0) {
            return null;
        }

        return $info->dwNumberOfProcessors;
    }

    public function tryGetCores(ArchitectureInterface $arch): ?int
    {
        if (\PHP_OS_FAMILY !== 'Windows' || !Runtime::isAvailable()) {
            return null;
        }

        $length = $this->kernel32->new('DWORD');

        $this->kernel32->GetLogicalProcessorInformation(null, \FFI::addr($length));

        /** @phpstan-ignore-next-line : The "$length->cdata" contain integer value */
        $count = (int) ($length->cdata / \FFI::sizeof($this->kernel32->type('SYSTEM_LOGICAL_PROCESSOR_INFORMATION')));

        $buffer = $this->kernel32->new("SYSTEM_LOGICAL_PROCESSOR_INFORMATION[$count]");

        $this->kernel32->GetLogicalProcessorInformation($buffer, \FFI::addr($length));

        $cores = 0;

        /**
         * @var iterable<mixed, CData&object{Relationship: int}> $buffer
         *
         * @phpstan-ignore-next-line : PHPStan false positive
         */
        foreach ($buffer as $logicalProcessorInfo) {
            if ($logicalProcessorInfo->Relationship === self::RELATION_PROCESSOR_CORE) {
                ++$cores;
            }
        }

        return $cores === 0 ? null : $cores;
    }
}
