<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

use Boson\Contracts\ValueObject\StringValueObjectInterface;

/**
 * @template-extends StringValueObjectInterface<non-empty-string>
 */
interface InstructionSetInterface extends StringValueObjectInterface
{
    /**
     * @var non-empty-string
     */
    public string $name {
        get;
    }
}
