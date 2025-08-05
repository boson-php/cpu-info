<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory;

use Boson\Component\CpuInfo\CentralProcessorInterface;

final class InMemoryCentralProcessorFactory implements CentralProcessorFactoryInterface
{
    private ?CentralProcessorInterface $current = null;

    public function __construct(
        private readonly CentralProcessorFactoryInterface $delegate,
    ) {}

    public function createCentralProcessor(): CentralProcessorInterface
    {
        return $this->current ??= $this->delegate->createCentralProcessor();
    }
}
