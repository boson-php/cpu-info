<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\Factory\Driver\WindowsRegistry\Advapi32;
use FFI\CData;
use FFI\Env\Runtime;

final readonly class WindowsRegistryDriver implements
    VendorDriverInterface,
    NameDriverInterface
{
    /**
     * restrict type to REG_SZ
     */
    private const int RRF_RT_REG_SZ = 0x00000002;

    /**
     * Contains registry root for HKLM
     *
     * ```
     *  ((HKEY)(LONG_PTR)(LONG)0x80000002)
     * ```
     */
    private const int HKEY_LOCAL_MACHINE = 0xFFFFFFFF << 32 | 0x80000002;

    /**
     * @var non-empty-string
     */
    private const string REG_PATH_CPU_INFO = 'HARDWARE\DESCRIPTION\System\CentralProcessor\0';

    private Advapi32 $advapi32;

    public function __construct()
    {
        $this->advapi32 = new \ReflectionClass(Advapi32::class)
            ->newLazyProxy(static fn(): Advapi32 => new Advapi32());
    }

    public function tryGetVendor(ArchitectureInterface $arch): ?string
    {
        if (\PHP_OS_FAMILY !== 'Windows' || !Runtime::isAvailable()) {
            return null;
        }

        $result = $this->getStringKey($this->advapi32, 'VendorIdentifier');

        if ($result === '') {
            return null;
        }

        return $result;
    }

    public function tryGetName(ArchitectureInterface $arch): ?string
    {
        if (\PHP_OS_FAMILY !== 'Windows' || !Runtime::isAvailable()) {
            return null;
        }

        $result = $this->getStringKey($this->advapi32, 'ProcessorNameString');

        if ($result === '') {
            return null;
        }

        return $result;
    }

    private function getStringKey(Advapi32 $advapi32, string $name): string
    {
        $buffer = $advapi32->new('char[255]');

        try {
            $size = $this->getKey($advapi32, $name, self::RRF_RT_REG_SZ, $buffer);
        } catch (\Throwable) {
            return '';
        }

        return \rtrim(\FFI::string($buffer, $size), "\0 ");
    }

    /**
     * @param int<0, 4294967295> $type
     *
     * @return int<0, 4294967295>
     */
    private function getKey(Advapi32 $advapi32, string $name, int $type, CData $buffer): int
    {
        $size = $advapi32->new('DWORD');
        $size->cdata = \FFI::sizeof($buffer);

        $status = $advapi32->RegGetValueA(
            $advapi32->cast('HKEY', self::HKEY_LOCAL_MACHINE),
            self::REG_PATH_CPU_INFO,
            $name,
            $type,
            null,
            \FFI::addr($buffer),
            \FFI::addr($size),
        );

        if ($status !== 0) {
            throw new \RuntimeException('Could not read registry key ' . $name);
        }

        /** @var int<0, 4294967295> */
        return $size->cdata;
    }
}
