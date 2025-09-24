<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver;

use Boson\Component\CpuInfo\ArchitectureInterface;

final readonly class EnvDriver implements
    NameDriverInterface,
    VendorDriverInterface,
    CoresDriverInterface,
    ThreadsDriverInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_NAME = 'BOSON_CPU_NAME';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_VENDOR = 'BOSON_CPU_VENDOR';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_CORES = 'BOSON_CPU_CORES';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_THREADS = 'BOSON_CPU_THREADS';

    public function __construct(
        /**
         * @var list<non-empty-string>
         */
        private array $nameEnvVariableNames = [],
        /**
         * @var list<non-empty-string>
         */
        private array $vendorEnvVariableNames = [],
        /**
         * @var list<non-empty-string>
         */
        private array $coresEnvVariableNames = [],
        /**
         * @var list<non-empty-string>
         */
        private array $threadsEnvVariableNames = [],
    ) {}

    /**
     * Creates an instance configured to use the default override
     * environment variable.
     */
    public static function createForOverrideEnvVariables(): self
    {
        return new self(
            nameEnvVariableNames: [self::DEFAULT_OVERRIDE_ENV_NAME],
            vendorEnvVariableNames: [self::DEFAULT_OVERRIDE_ENV_VENDOR],
            coresEnvVariableNames: [self::DEFAULT_OVERRIDE_ENV_CORES],
            threadsEnvVariableNames: [self::DEFAULT_OVERRIDE_ENV_THREADS],
        );
    }

    public function tryGetName(ArchitectureInterface $arch): ?string
    {
        return $this->tryGetEnvironmentAsString($this->nameEnvVariableNames);
    }

    public function tryGetVendor(ArchitectureInterface $arch): ?string
    {
        return $this->tryGetEnvironmentAsString($this->vendorEnvVariableNames);
    }

    public function tryGetCores(ArchitectureInterface $arch): ?int
    {
        $result = $this->tryGetEnvironmentAsInt($this->coresEnvVariableNames);

        return $result === null ? null : \max(1, $result);
    }

    public function tryGetThreads(ArchitectureInterface $arch): ?int
    {
        $result = $this->tryGetEnvironmentAsInt($this->threadsEnvVariableNames);

        return $result === null ? null : \max(1, $result);
    }

    /**
     * @param iterable<mixed, non-empty-string> $envVariables
     */
    private function tryGetEnvironmentAsInt(iterable $envVariables): ?int
    {
        $result = $this->tryGetEnvironmentAsString($envVariables);

        if ($result !== null) {
            return (int) $result;
        }

        return null;
    }

    /**
     * @param iterable<mixed, non-empty-string> $envVariables
     *
     * @return non-empty-string|null
     */
    private function tryGetEnvironmentAsString(iterable $envVariables): ?string
    {
        foreach ($envVariables as $name) {
            $server = $_SERVER[$name] ?? null;

            if (\is_string($server) && $server !== '') {
                return $server;
            }
        }

        return null;
    }
}
