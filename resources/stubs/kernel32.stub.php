<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver\WindowsSysInfo;

use FFI\CData;
use FFI\CType;

/**
 * @mixin \FFI
 * @seal-properties
 * @seal-methods
 */
final readonly class Kernel32
{
    /**
     * @param CType|non-empty-string $type
     *
     * @return ($type is "SYSTEM_INFO" ? __SystemInfoStruct : CData)
     */
    public function new(CType|string $type, bool $owned = true, bool $persistent = false): CData {}

    /**
     * @param CType|non-empty-string $type
     */
    public function cast(CType|string $type, CData|int|float|bool|null $ptr): CData {}

    /**
     * @param CData $addr
     */
    public function GetNativeSystemInfo(CData $addr): void {}

    public function GetLogicalProcessorInformation(?CData $buffer, ?CData $length): void {}
}

class __SystemInfoStruct extends CData
{
    /**
     * @var int<0, max>
     */
    public int $dwNumberOfProcessors = 0;
}