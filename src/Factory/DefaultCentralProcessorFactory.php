<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory;

use Boson\Component\CpuInfo\Architecture\Factory\ArchitectureFactoryInterface;
use Boson\Component\CpuInfo\Architecture\Factory\DefaultArchitectureFactory;
use Boson\Component\CpuInfo\CentralProcessor;
use Boson\Component\CpuInfo\Factory\Driver\CpuIdDriver;
use Boson\Component\CpuInfo\Factory\Driver\EnvDriver;
use Boson\Component\CpuInfo\Factory\Driver\GenericDriver;
use Boson\Component\CpuInfo\Factory\Driver\LinuxProcCpuInfoDriver;
use Boson\Component\CpuInfo\Factory\Driver\WindowsRegistryDriver;
use Boson\Component\CpuInfo\Factory\Driver\WindowsSysInfoDriver;

final readonly class DefaultCentralProcessorFactory implements CentralProcessorFactoryInterface
{
    private CentralProcessorFactoryInterface $factory;

    public function __construct(
        ArchitectureFactoryInterface $architectureFactory = new DefaultArchitectureFactory(),
    ) {
        $this->factory = new CentralProcessorFactory(
            architectureFactory: $architectureFactory,
            drivers: [
                EnvDriver::createForOverrideEnvVariables(),
                new LinuxProcCpuInfoDriver(),
                new WindowsRegistryDriver(),
                new WindowsSysInfoDriver(),
                new CpuIdDriver(),
                new EnvDriver([], ['PROCESSOR_IDENTIFIER'], ['NUMBER_OF_PROCESSORS'], ['NUMBER_OF_PROCESSORS']),
                new GenericDriver(),
            ]
        );
    }

    public function createCentralProcessor(): CentralProcessor
    {
        return $this->factory->createCentralProcessor();
    }
}
