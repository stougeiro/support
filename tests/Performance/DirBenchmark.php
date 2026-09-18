<?php

use STDW\Support\Dir;

/*
|--------------------------------------------------------------------------
| Performance Benchmarks for Dir
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
    $tmp = sys_get_temp_dir() . '/dir_bench_' . uniqid();
    mkdir($tmp);

    for ($i = 0; $i < $count; $i++) {
        file_put_contents($tmp . "/file_{$i}.txt", str_repeat('x', 100));
    }

    return $tmp;
}

// ---------------------------------------------------------------
// rrmdir()
// ---------------------------------------------------------------

test('bench rrmdir 100 files', function () {
    $us = bench(function () {
        $tmp = createTempDirWithFiles(100);
        Dir::rrmdir($tmp);
    }, 10);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// absolutePath()
// ---------------------------------------------------------------

test('bench absolutePath simple', function () {
    $us = bench(fn() => Dir::absolutePath('foo/bar/baz'), 1_000);

    expect($us)->toBeFloat();
});

test('bench absolutePath with dot-dot', function () {
    $us = bench(fn() => Dir::absolutePath('foo/bar/../baz/qux/../../end'), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// create()
// ---------------------------------------------------------------

test('bench create nested', function () {
    $us = bench(function () {
        $tmp = sys_get_temp_dir() . '/dir_bench_create_' . uniqid() . '/a/b/c/d/e';
        Dir::create($tmp);
        Dir::rrmdir(sys_get_temp_dir() . '/dir_bench_create_' . uniqid());
    }, 100);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// size()
// ---------------------------------------------------------------

test('bench size 100 files', function () {
    $tmp = createTempDirWithFiles(100);

    $us = bench(fn() => Dir::size($tmp), 100);

    Dir::rrmdir($tmp);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// copy()
// ---------------------------------------------------------------

test('bench copy 100 files', function () {
    $us = bench(function () {
        $src = createTempDirWithFiles(100);
        $dst = sys_get_temp_dir() . '/dir_bench_copy_dst_' . uniqid();
        Dir::copy($src, $dst);
        Dir::rrmdir($src);
        Dir::rrmdir($dst);
    }, 10);

    expect($us)->toBeFloat();
});
