<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver\LinuxProcCpuInfo;

/**
 * @phpstan-type ThreadInfoType array<non-empty-string, string>
 * @phpstan-type CoreInfoType array<int, ThreadInfoType>
 * @phpstan-type ProcessorInfoType iterable<int, CoreInfoType>
 *
 * @template-implements \IteratorAggregate<int, ProcessorInfoType>
 */
final readonly class LinuxProcCpuInfoReader implements \IteratorAggregate
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        private string $pathname,
    ) {}

    /**
     * @return \Generator<mixed, ProcessorInfoType>
     */
    public function getIterator(): \Generator
    {
        $result = [];

        foreach ($this->readSegments() as $segment) {
            $result[(int) $segment['physical id']][(int) $segment['core id']][] = $segment;
        }

        yield from $result;
    }

    /**
     * @return iterable<array-key, ThreadInfoType>
     */
    private function readSegments(): iterable
    {
        $current = [];

        foreach ($this->readLines() as $line) {
            $offset = \strpos($line, ':');

            if ($offset === false) {
                if ($current !== []) {
                    yield $current;
                    $current = [];
                }

                continue;
            }

            $key = \trim(\substr($line, 0, $offset));

            if ($key === '') {
                continue;
            }

            $value = \trim(\substr($line, $offset + 1));

            $current[$key] = $value;
        }

        if ($current !== []) {
            yield $current;
        }
    }

    /**
     * @return iterable<array-key, string>
     */
    private function readLines(): iterable
    {
        if (!\is_readable($this->pathname)) {
            return [];
        }

        $proc = @\fopen($this->pathname, 'rb');

        if ($proc === false) {
            return [];
        }

        while (!\feof($proc)) {
            yield (string) \fgets($proc);
        }

        \fclose($proc);
    }
}
