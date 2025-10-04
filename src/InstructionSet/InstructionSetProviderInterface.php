<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet;

use Boson\Component\CpuInfo\InstructionSetInterface;

interface InstructionSetProviderInterface
{
    /**
     * Returns a list of supported processor instructions.
     *
     * @var iterable<array-key, InstructionSetInterface>
     */
    public iterable $instructionSets { get; }
}
