<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture\Factory;

use Boson\Component\CpuInfo\ArchitectureInterface;

final readonly class DefaultArchitectureFactory implements ArchitectureFactoryInterface
{
    private ArchitectureFactoryInterface $default;

    public function __construct()
    {
        $this->default = EnvArchitectureFactory::createForOverrideEnvVariables(
            delegate: new EnvArchitectureFactory(
                delegate: new GenericArchitectureFactory(),
                envVariableNames: ['PROCESSOR_ARCHITECTURE'],
            ),
        );
    }

    public function createArchitecture(): ArchitectureInterface
    {
        return $this->default->createArchitecture();
    }
}
