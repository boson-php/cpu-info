<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Internal;

final readonly class LinuxProcCpuInfoReader
{
    /**
     * @var non-empty-string
     */
    private const string DEFAULT_CPU_INFO_FILE = '/proc/cpuinfo';

    public static function isReadable(): bool
    {
        return \is_readable(self::DEFAULT_CPU_INFO_FILE);
    }

    /**
     * @return list<array<non-empty-string, string>>
     */
    public function read(): array
    {
        $segments = $current = [];

        foreach ($this->readLines() as $line) {
            $offset = \strpos($line, ':');

            if ($offset === false) {
                if ($current !== []) {
                    $segments[] = $current;
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
            $segments[] = $current;
        }

        return $segments;
    }

    /**
     * @return iterable<array-key, string>
     */
    private function readLines(): iterable
    {
        $proc = @\fopen(self::DEFAULT_CPU_INFO_FILE, 'rb');

        if ($proc === false) {
            return [];
        }

        while (!\feof($proc)) {
            yield (string) \fgets($proc);
        }

        \fclose($proc);
    }
}
