<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor;

readonly class VendorInfo implements VendorInfoInterface, \Stringable
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $name,
        /**
         * @var non-empty-string|null
         */
        public ?string $vendor = null,
        /**
         * @var int<1, max>
         */
        public int $physicalCores = 1,
        /**
         * @var int<1, max>
         */
        public int $logicalCores = 1,
    ) {}

    public function __toString(): string
    {
        return $this->name;
    }
}
