<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Tests;

use Boson\Component\CpuInfo\Architecture\Factory\ArchitectureFactoryInterface;
use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\CentralProcessor;
use Boson\Component\CpuInfo\Factory\CentralProcessorFactoryInterface;
use Boson\Component\CpuInfo\Factory\Driver\CpuId\DetectorInterface;
use Boson\Component\CpuInfo\InstructionSetInterface;
use Boson\Component\Pasm\ExecutorInterface;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;

/**
 * Note: Changing the behavior of these tests is allowed ONLY when updating
 *       a MAJOR version of the package.
 */
#[Group('boson-php/cpu-info')]
final class CompatibilityTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testArchitectureInterfaceCompatibility(): void
    {
        new class implements ArchitectureInterface {
            public string $name {
                get {}
            }
            public ?ArchitectureInterface $parent {
                get {}
            }

            public function is(ArchitectureInterface $arch): bool {}

            public function equals(mixed $other): bool {}

            public function toString(): string {}

            public function __toString(): string {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testInstructionSetInterfaceCompatibility(): void
    {
        new class implements InstructionSetInterface {
            public string $name {
                get {}
            }

            public function equals(mixed $other): bool {}

            public function toString(): string {}

            public function __toString(): string {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testDetectorInterfaceCompatibility(): void
    {
        new class implements DetectorInterface {
            public function isSupported(ArchitectureInterface $arch): bool {}

            public function detect(ExecutorInterface $executor): ?InstructionSetInterface {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testArchitectureFactoryInterfaceCompatibility(): void
    {
        new class implements ArchitectureFactoryInterface {
            public function createArchitecture(): ArchitectureInterface {}
        };
    }

    #[DoesNotPerformAssertions]
    public function testCentralProcessorFactoryInterfaceCompatibility(): void
    {
        new class implements CentralProcessorFactoryInterface {
            public function createCentralProcessor(): CentralProcessor {}
        };
    }
}
