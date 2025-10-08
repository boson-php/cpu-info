<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory\Driver\WindowsSysInfo;

use FFI\Env\Runtime;

final readonly class Kernel32
{
    private \FFI $ffi;

    public function __construct()
    {
        Runtime::assertAvailable();

        $this->ffi = \FFI::cdef((string) @\file_get_contents(
            filename: __FILE__,
            offset: __COMPILER_HALT_OFFSET__,
        ), 'kernel32.dll');
    }

    /**
     * @param non-empty-string $name
     * @param array<array-key, mixed> $arguments
     */
    public function __call(string $name, array $arguments = []): mixed
    {
        try {
            return $this->ffi->$name(...$arguments);
        } catch (\Throwable $e) {
            throw new \BadMethodCallException($e->getMessage(), previous: $e);
        }
    }
}

__halt_compiler();

typedef unsigned long DWORD;
typedef DWORD *PDWORD;
typedef unsigned short WORD;
typedef uintptr_t ULONG_PTR;
typedef ULONG_PTR DWORD_PTR;
typedef void *LPVOID;
typedef unsigned char BYTE;
typedef uint64_t ULONGLONG;
typedef bool BOOL;      // typedef int BOOL;

typedef struct _SYSTEM_INFO {
    WORD wProcessorArchitecture;
    WORD wReserved;
    DWORD     dwPageSize;
    LPVOID    lpMinimumApplicationAddress;
    LPVOID    lpMaximumApplicationAddress;
    DWORD_PTR dwActiveProcessorMask;
    DWORD     dwNumberOfProcessors;
    DWORD     dwProcessorType;
    DWORD     dwAllocationGranularity;
    WORD      wProcessorLevel;
    WORD      wProcessorRevision;
} SYSTEM_INFO,
  *LPSYSTEM_INFO;

typedef enum _LOGICAL_PROCESSOR_RELATIONSHIP {
    RelationProcessorCore,
    RelationNumaNode,
    RelationCache,
    RelationProcessorPackage,
    RelationGroup,
    RelationProcessorDie,
    RelationNumaNodeEx,
    RelationProcessorModule,
    RelationAll = 0xffff
} LOGICAL_PROCESSOR_RELATIONSHIP;

typedef enum _PROCESSOR_CACHE_TYPE {
    CacheUnified,
    CacheInstruction,
    CacheData,
    CacheTrace,
    CacheUnknown
} PROCESSOR_CACHE_TYPE,
  *PPROCESSOR_CACHE_TYPE;

typedef struct _CACHE_DESCRIPTOR {
    BYTE                 Level;
    BYTE                 Associativity;
    WORD                 LineSize;
    DWORD                Size;
    PROCESSOR_CACHE_TYPE Type;
} CACHE_DESCRIPTOR,
  *PCACHE_DESCRIPTOR;

typedef struct _SYSTEM_LOGICAL_PROCESSOR_INFORMATION {
    ULONG_PTR                      ProcessorMask;
    LOGICAL_PROCESSOR_RELATIONSHIP Relationship;
    union {
        struct {
            BYTE Flags;
        } ProcessorCore;
        struct {
            DWORD NodeNumber;
        } NumaNode;
        CACHE_DESCRIPTOR Cache;
        ULONGLONG        Reserved[2];
    } DUMMYUNIONNAME;
} SYSTEM_LOGICAL_PROCESSOR_INFORMATION,
  *PSYSTEM_LOGICAL_PROCESSOR_INFORMATION;


void GetNativeSystemInfo(
    LPSYSTEM_INFO lpSystemInfo
);

BOOL GetLogicalProcessorInformation(
    PSYSTEM_LOGICAL_PROCESSOR_INFORMATION Buffer,
    PDWORD                                ReturnedLength
);