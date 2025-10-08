<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

use Boson\Component\CpuInfo\Factory\DefaultCentralProcessorFactory;
use Boson\Component\CpuInfo\Factory\InMemoryCentralProcessorFactory;

final readonly class CentralProcessor
{
    /**
     * Gets list of supported processor instructions
     *
     * @var list<InstructionSetInterface>
     */
    public array $instructionSets;

    /**
     * @param iterable<mixed, InstructionSetInterface> $instructionSets
     */
    public function __construct(
        /**
         * Gets current CPU architecture type
         */
        public ArchitectureInterface $arch,
        /**
         * Gets current CPU generic vendor name.
         *
         * @var non-empty-string
         */
        public string $vendor,
        /**
         * Gets current CPU name.
         *
         * @var non-empty-string|null
         */
        public ?string $name = null,
        /**
         * Gets the number of physical CPU cores.
         *
         * @var int<1, max>
         */
        public int $cores = 1,
        /**
         * Gets the number of logical CPU cores.
         *
         * Note: The number of logical cores can be equal to or greater
         *       than the number of physical cores ({@see $cores}).
         *
         * @var int<1, max>
         */
        public int $threads = 1,
        iterable $instructionSets = [],
    ) {
        $this->instructionSets = \iterator_to_array($instructionSets, false);
    }

    /**
     * @api
     */
    public static function createFromGlobals(): CentralProcessor
    {
        /** @phpstan-var InMemoryCentralProcessorFactory $factory */
        static $factory = new InMemoryCentralProcessorFactory(
            delegate: new DefaultCentralProcessorFactory(),
        );

        return $factory->createCentralProcessor();
    }

    /**
     * Checks if this CPU supports the given instruction set.
     *
     * @api
     */
    public function isSupports(InstructionSetInterface $instructionSet): bool
    {
        return \in_array($instructionSet, $this->instructionSets, true);
    }
}
