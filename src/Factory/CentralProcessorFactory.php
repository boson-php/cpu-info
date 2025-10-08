<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory;

use Boson\Component\CpuInfo\Architecture\Factory\ArchitectureFactoryInterface;
use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\CentralProcessor;
use Boson\Component\CpuInfo\Factory\Driver\CoresDriverInterface;
use Boson\Component\CpuInfo\Factory\Driver\InstructionSetsDriverInterface;
use Boson\Component\CpuInfo\Factory\Driver\NameDriverInterface;
use Boson\Component\CpuInfo\Factory\Driver\ThreadsDriverInterface;
use Boson\Component\CpuInfo\Factory\Driver\VendorDriverInterface;
use Boson\Component\CpuInfo\InstructionSetInterface;

final readonly class CentralProcessorFactory implements CentralProcessorFactoryInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_UNKNOWN_VENDOR = 'unknown';

    /**
     * @var list<object>
     */
    private array $drivers;

    /**
     * @param iterable<mixed, object> $drivers
     * @param non-empty-string $unknownVendor
     */
    public function __construct(
        private ArchitectureFactoryInterface $architectureFactory,
        iterable $drivers = [],
        private string $unknownVendor = self::DEFAULT_UNKNOWN_VENDOR,
    ) {
        $this->drivers = \iterator_to_array($drivers, false);
    }

    public function createCentralProcessor(): CentralProcessor
    {
        $arch = $this->architectureFactory->createArchitecture();

        $cores = $this->getCores($arch);
        $threads = \max($cores, $this->getThreads($arch));

        return new CentralProcessor(
            arch: $arch,
            vendor: $this->getVendor($arch),
            name: $this->getName($arch),
            cores: $cores,
            threads: $threads,
            instructionSets: $this->getInstructionSets($arch),
        );
    }

    /**
     * @template TArg of object
     * @template TResult of mixed
     *
     * @param class-string<TArg> $type
     * @param \Closure(TArg):(TResult|null) $then
     *
     * @return TResult|null
     */
    private function filter(string $type, \Closure $then): mixed
    {
        foreach ($this->drivers as $driver) {
            if (!$driver instanceof $type) {
                continue;
            }

            $result = $then($driver);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @return non-empty-string
     */
    private function getVendor(ArchitectureInterface $arch): string
    {
        return $this->filter(
            type: VendorDriverInterface::class,
            then: fn(VendorDriverInterface $driver): ?string => $driver->tryGetVendor($arch),
        ) ?? $this->unknownVendor;
    }

    /**
     * @return non-empty-string|null
     */
    private function getName(ArchitectureInterface $arch): ?string
    {
        return $this->filter(
            type: NameDriverInterface::class,
            then: fn(NameDriverInterface $driver): ?string => $driver->tryGetName($arch),
        );
    }

    /**
     * @return int<1, max>
     */
    private function getCores(ArchitectureInterface $arch): int
    {
        return $this->filter(
            type: CoresDriverInterface::class,
            then: fn(CoresDriverInterface $driver): ?int => $driver->tryGetCores($arch),
        ) ?? 1;
    }

    /**
     * @return int<1, max>
     */
    private function getThreads(ArchitectureInterface $arch): int
    {
        return $this->filter(
            type: ThreadsDriverInterface::class,
            then: fn(ThreadsDriverInterface $driver): ?int => $driver->tryGetThreads($arch),
        ) ?? 1;
    }

    /**
     * @return iterable<array-key, InstructionSetInterface>
     */
    private function getInstructionSets(ArchitectureInterface $arch): iterable
    {
        return $this->filter(
            type: InstructionSetsDriverInterface::class,
            then: fn(InstructionSetsDriverInterface $driver): ?iterable => $driver->tryGetInstructionSets($arch),
        ) ?? [];
    }
}
