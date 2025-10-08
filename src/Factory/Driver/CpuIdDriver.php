<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\AESDetector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\AVX2Detector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\AVX512FDetector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\AVXDetector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\DetectorInterface;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\EM64TDetector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\F16CDetector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\FMA3Detector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\MMXDetector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\POPCNTDetector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\SSE2Detector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\SSE3Detector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\SSE41Detector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\SSE42Detector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\SSEDetector;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\SSSE3Detector;
use Boson\Component\Pasm\Executor;
use Boson\Component\Pasm\ExecutorInterface;

final readonly class CpuIdDriver implements InstructionSetsDriverInterface
{
    public function __construct(
        private ExecutorInterface $executor = new Executor(),
    ) {}

    /**
     * @return list<DetectorInterface>
     */
    private function getDetectors(): array
    {
        return [
            new MMXDetector(),
            new SSEDetector(),
            new SSE2Detector(),
            new SSE3Detector(),
            new SSSE3Detector(),
            new SSE41Detector(),
            new SSE42Detector(),
            new FMA3Detector(),
            new AVXDetector(),
            new AVX2Detector(),
            new AVX512FDetector(),
            new AESDetector(),
            new EM64TDetector(),
            new POPCNTDetector(),
            new F16CDetector(),
        ];
    }

    public function tryGetInstructionSets(ArchitectureInterface $arch): ?iterable
    {
        $result = [];

        foreach ($this->getDetectors() as $detector) {
            if ($detector->isSupported($arch)) {
                $instructionSet = $detector->detect($this->executor);

                if ($instructionSet !== null) {
                    $result[] = $instructionSet;
                }
            }
        }

        if ($result === []) {
            return null;
        }

        return $result;
    }
}
