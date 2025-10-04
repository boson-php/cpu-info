<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Internal\LinuxProcCpuInfoReader;
use Boson\Component\CpuInfo\Vendor\VendorInfo;
use Boson\Component\CpuInfo\Vendor\VendorInfoInterface;
use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface as OsFamilyFactoryInterface;

final readonly class LinuxProcCpuInfoVendorFactory implements VendorFactoryInterface
{
    public function __construct(
        private VendorFactoryInterface $delegate,
        private ?OsFamilyFactoryInterface $osFamilyFactory = null,
    ) {}

    public function createVendor(): VendorInfoInterface
    {
        $family = $this->osFamilyFactory?->createFamilyFromGlobals()
            ?? Family::createFromGlobals();

        $fallback = $this->delegate->createVendor();

        if (!$family->is(Family::Linux) || !LinuxProcCpuInfoReader::isReadable()) {
            return $fallback;
        }

        return $this->tryCreateFromProcCpuInfo($fallback);
    }

    private function tryCreateFromProcCpuInfo(VendorInfoInterface $fallback): VendorInfoInterface
    {
        $processors = new LinuxProcCpuInfoReader()
            ->read();

        $name = $this->getFirstProcessorName($processors);

        if ($name === null || $name === '') {
            return $fallback;
        }

        return new VendorInfo(
            name: $name,
            vendor: $this->getFirstProcessorVendor($processors)
                ?? $fallback->vendor,
            physicalCores: $this->getProcessorPhysicalCores($processors)
                ?? $fallback->physicalCores,
            logicalCores: $this->getProcessorLogicalCores($processors)
                ?? $fallback->logicalCores,
        );
    }

    /**
     * @param list<array<non-empty-string, string>> $processors
     *
     * @return int<1, max>|null
     */
    private function getProcessorPhysicalCores(array $processors): ?int
    {
        $physicalCores = [];

        foreach ($processors as $processor) {
            // Key is "<CPU ID> : <PHYSICAL CORE ID> : <LOGICAL CORE ID>"
            $index = ($processor['physical id'] ?? '0') . ':'
                . ($processor['core id'] ?? '0');

            $physicalCores[$index] = true;
        }

        $result = \count($physicalCores);

        return $result === 0 ? null : $result;
    }

    /**
     * @param list<array<non-empty-string, string>> $processors
     *
     * @return int<1, max>|null
     */
    private function getProcessorLogicalCores(array $processors): ?int
    {
        $logicalCores = [];

        foreach ($processors as $processor) {
            // Key is "<CPU ID> : <PHYSICAL CORE ID>"
            $index = ($processor['processor'] ?? '0') . ':'
                . ($processor['physical id'] ?? '0');

            $logicalCores[$index] = true;
        }

        $result = \count($logicalCores);

        return $result === 0 ? null : $result;
    }

    /**
     * Gets first found CPU name
     *
     * @param list<array<non-empty-string, string>> $processors
     *
     * @return non-empty-string|null
     */
    private function getFirstProcessorName(array $processors): ?string
    {
        foreach ($processors as $processor) {
            $name = $processor['model name'] ?? null;

            if ($name !== null && $name !== '') {
                return $name;
            }
        }

        return null;
    }

    /**
     * Gets first found CPU`s vendor name
     *
     * @param list<array<non-empty-string, string>> $processors
     *
     * @return non-empty-string|null
     */
    private function getFirstProcessorVendor(array $processors): ?string
    {
        foreach ($processors as $processor) {
            $vendor = $processor['vendor_id'] ?? null;

            if ($vendor !== null && $vendor !== '') {
                return $vendor;
            }
        }

        return null;
    }
}
