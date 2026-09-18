<?php

use STDW\Support\FileSystem;

/*
|--------------------------------------------------------------------------
| Performance Benchmarks for FileSystem
|--------------------------------------------------------------------------
|
| Each test measures execution time using microtime(true).
| Tests run with multiple iterations to stabilize results.
| Output shows average time per call in microseconds.
|
*/

// ---------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------

function bench(callable $fn, int $iterations = 100): float
{
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $fn();
    }

    $total = microtime(true) - $start;

    return ($total / $iterations) * 1_000_000; // microseconds per call
}

function createTempDirWithFiles(int $count): string
{
    $tmp = sys_get_temp_dir() . '/fs_bench_' . uniqid();
    mkdir($tmp);

    for ($i = 0; $i < $count; $i++) {
        file_put_contents($tmp . "/file_{$i}.txt", str_repeat('x', 100));
    }

    return $tmp;
}

// ---------------------------------------------------------------
// absolute()
// ---------------------------------------------------------------

test('bench absolute simple', function () {
    $us = bench(fn() => FileSystem::absolute('foo/bar/baz'), 1_000);

    expect($us)->toBeFloat();
});

test('bench absolute with dot-dot', function () {
    $us = bench(fn() => FileSystem::absolute('foo/bar/../baz/qux/../../end'), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// size()
// ---------------------------------------------------------------

test('bench size single file', function () {
    $tmp = sys_get_temp_dir() . '/fs_bench_size_file_' . uniqid();
    file_put_contents($tmp, str_repeat('x', 1024));

    $us = bench(fn() => FileSystem::size($tmp), 1_000);

    unlink($tmp);

    expect($us)->toBeFloat();
});

test('bench size directory 100 files', function () {
    $tmp = createTempDirWithFiles(100);

    $us = bench(fn() => FileSystem::size($tmp), 100);

    FileSystem::rrmdir($tmp);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// rrmdir()
// ---------------------------------------------------------------

test('bench rrmdir 100 files', function () {
    $us = bench(function () {
        $tmp = createTempDirWithFiles(100);
        FileSystem::rrmdir($tmp);
    }, 10);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// copy()
// ---------------------------------------------------------------

test('bench copy 100 files', function () {
    $us = bench(function () {
        $src = createTempDirWithFiles(100);
        $dst = sys_get_temp_dir() . '/fs_bench_copy_dst_' . uniqid();
        FileSystem::copy($src, $dst);
        FileSystem::rrmdir($src);
        FileSystem::rrmdir($dst);
    }, 10);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// rglob()
// ---------------------------------------------------------------

test('bench rglob recursive', function () {
    $tmp = createTempDirWithFiles(50);
    mkdir($tmp . '/sub');
    for ($i = 0; $i < 50; $i++) {
        file_put_contents($tmp . "/sub/file_{$i}.txt", 'x');
    }

    $us = bench(fn() => FileSystem::rglob($tmp . '/*.txt'), 100);

    FileSystem::rrmdir($tmp);

    expect($us)->toBeFloat();
});
