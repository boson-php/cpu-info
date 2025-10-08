<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\Factory\Driver\LinuxProcCpuInfo\LinuxProcCpuInfoReader;
use Boson\Component\CpuInfo\InstructionSet;

final class LinuxProcCpuInfoDriver implements
    NameDriverInterface,
    VendorDriverInterface,
    CoresDriverInterface,
    ThreadsDriverInterface,
    InstructionSetsDriverInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_CPU_INFO_FILE = '/proc/cpuinfo';

    private bool $booted = false;

    /**
     * @var non-empty-string|null
     */
    private ?string $name = null;

    /**
     * @var non-empty-string|null
     */
    private ?string $vendor = null;

    /**
     * @var int<0, max>
     */
    private int $cores = 0;

    /**
     * @var int<0, max>
     */
    private int $threads = 0;

    /**
     * @var non-empty-string|null
     */
    private ?string $instructions = null;

    public function __construct(
        /**
         * @var non-empty-string
         */
        private readonly string $pathname = self::DEFAULT_CPU_INFO_FILE,
    ) {}

    private function bootIfNotBooted(): void
    {
        if ($this->booted) {
            return;
        }

        // We read only the FIRST available physical CPU
        foreach (new LinuxProcCpuInfoReader($this->pathname) as $processors) {
            foreach ($processors as $cores) {
                foreach ($cores as $thread) {
                    $this->loadNameFromSegment($thread);
                    $this->loadVendorFromSegment($thread);
                    $this->loadInstructionsFromSegment($thread);

                    ++$this->threads;
                }

                ++$this->cores;
            }

            break;
        }

        $this->booted = true;
    }

    /**
     * @param array<array-key, string> $segment
     */
    private function loadVendorFromSegment(array $segment): void
    {
        // Load CPU Vendor
        if ($this->vendor !== null) {
            return;
        }

        $vendorString = \trim($segment['vendor_id'] ?? '');

        if ($vendorString === '') {
            return;
        }

        $this->vendor = $vendorString;
    }

    /**
     * @param array<array-key, string> $segment
     */
    private function loadNameFromSegment(array $segment): void
    {
        if ($this->name !== null) {
            return;
        }

        $nameString = \trim($segment['model name'] ?? '');

        if ($nameString === '') {
            return;
        }

        $this->name = $nameString;
    }

    /**
     * @param array<array-key, string> $segment
     */
    private function loadInstructionsFromSegment(array $segment): void
    {
        if ($this->instructions !== null) {
            return;
        }

        $instructionsString = \trim($segment['flags'] ?? '');

        if ($instructionsString === '') {
            return;
        }

        $this->instructions = $instructionsString;
    }

    public function tryGetVendor(ArchitectureInterface $arch): ?string
    {
        $this->bootIfNotBooted();

        return $this->vendor;
    }

    public function tryGetName(ArchitectureInterface $arch): ?string
    {
        $this->bootIfNotBooted();

        return $this->name;
    }

    public function tryGetCores(ArchitectureInterface $arch): ?int
    {
        $this->bootIfNotBooted();

        return $this->cores > 0 ? $this->cores : null;
    }

    public function tryGetThreads(ArchitectureInterface $arch): ?int
    {
        $this->bootIfNotBooted();

        return $this->threads > 0 ? $this->threads : null;
    }

    public function tryGetInstructionSets(ArchitectureInterface $arch): ?iterable
    {
        $this->bootIfNotBooted();

        if ($this->instructions === null) {
            return null;
        }

        $result = [];

        /**
         * @link https://git.kernel.org/pub/scm/linux/kernel/git/stable/linux.git/tree/arch/x86/include/asm/cpufeature.h?id=refs/tags/v4.1.3
         */
        foreach (\explode(' ', $this->instructions) as $flag) {
            $instruction = match (\strtolower($flag)) {
                'mmx' => InstructionSet::MMX,
                'sse' => InstructionSet::SSE,
                'sse2' => InstructionSet::SSE2,
                'pni', 'sse3' => InstructionSet::SSE3,
                'ssse3' => InstructionSet::SSSE3,
                'sse4_1' => InstructionSet::SSE4_1,
                'sse4_2' => InstructionSet::SSE4_2,
                'avx' => InstructionSet::AVX,
                'avx2' => InstructionSet::AVX2,
                'avx512f' => InstructionSet::AVX512F,
                'aes' => InstructionSet::AES,
                'lm' => InstructionSet::EM64T,
                'popcnt' => InstructionSet::POPCNT,
                'f16c' => InstructionSet::F16C,
                default => null,
            };

            if ($instruction !== null) {
                $result[$flag] = $instruction;
            }
        }

        return $result;
    }
}
