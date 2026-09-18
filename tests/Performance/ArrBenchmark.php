<?php

use STDW\Support\Arr;

/*
|--------------------------------------------------------------------------
| Performance Benchmarks for Arr
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

function generateArray(int $size): array
{
    $arr = [];

    for ($i = 0; $i < $size; $i++) {
        $arr[$i] = $i;
    }

    return $arr;
}

function generateNestedArray(int $depth): array
{
    $arr = [1];

    for ($i = 0; $i < $depth; $i++) {
        $arr = [$arr];
    }

    return $arr;
}

function generateDotArray(int $count): array
{
    $arr = [];

    for ($i = 0; $i < $count; $i++) {
        $arr["level1.level2.key{$i}"] = $i;
    }

    return $arr;
}

// ---------------------------------------------------------------
// empty()
// ---------------------------------------------------------------

test('bench empty small', function () {
    $arr = [1];

    $us = bench(fn() => Arr::empty($arr));

    expect($us)->toBeFloat();
});

test('bench empty large', function () {
    $arr = generateArray(100_000);

    $us = bench(fn() => Arr::empty($arr));

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// kshift() / kpop()
// ---------------------------------------------------------------

test('bench kshift drain 10k', function () {
    $us = bench(function () {
        $arr = generateArray(10_000);

        while (Arr::kshift($arr) !== null) {
            // drain
        }
    }, 10);

    expect($us)->toBeFloat();
});

test('bench kpop drain 10k', function () {
    $us = bench(function () {
        $arr = generateArray(10_000);

        while (Arr::kpop($arr) !== null) {
            // drain
        }
    }, 10);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// grab()
// ---------------------------------------------------------------

test('bench grab 10k random keys', function () {
    $arr = generateArray(10_000);

    $us = bench(function () use ($arr) {
        $copy = $arr;

        for ($i = 0; $i < 10_000; $i++) {
            Arr::grab($i, $copy);
        }
    }, 10);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// flatten()
// ---------------------------------------------------------------

test('bench flatten depth 100', function () {
    $arr = generateNestedArray(100);

    $us = bench(fn() => Arr::flatten($arr), 100);

    expect($us)->toBeFloat();
});

test('bench flatten depth 500', function () {
    $arr = generateNestedArray(500);

    $us = bench(fn() => Arr::flatten($arr), 100);

    expect($us)->toBeFloat();
});

test('bench flatten depth 1000', function () {
    $arr = generateNestedArray(1000);

    $us = bench(fn() => Arr::flatten($arr), 100);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// any() / all() / find() / findKey()
// ---------------------------------------------------------------

test('bench any match last element', function () {
    $arr = generateArray(10_000);

    $us = bench(fn() => Arr::any($arr, fn($v) => $v === 9_999), 100);

    expect($us)->toBeFloat();
});

test('bench all all pass', function () {
    $arr = generateArray(10_000);

    $us = bench(fn() => Arr::all($arr, fn($v) => is_int($v)), 100);

    expect($us)->toBeFloat();
});

test('bench find match last element', function () {
    $arr = generateArray(10_000);

    $us = bench(fn() => Arr::find($arr, fn($v) => $v === 9_999), 100);

    expect($us)->toBeFloat();
});

test('bench findKey match last element', function () {
    $arr = generateArray(10_000);

    $us = bench(fn() => Arr::findKey($arr, fn($v) => $v === 9_999), 100);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// dot()
// ---------------------------------------------------------------

test('bench dot depth 3', function () {
    $arr = ['a' => ['b' => ['c' => 1, 'd' => 2, 'e' => 3]]];

    $us = bench(fn() => Arr::dot($arr), 1_000);

    expect($us)->toBeFloat();
});

test('bench dot depth 5', function () {
    $arr = ['a' => ['b' => ['c' => ['d' => ['e' => 1]]]]];

    $us = bench(fn() => Arr::dot($arr), 1_000);

    expect($us)->toBeFloat();
});

test('bench dot depth 10', function () {
    $arr = generateNestedArray(10);

    $us = bench(fn() => Arr::dot($arr), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// undot()
// ---------------------------------------------------------------

test('bench undot 100 keys', function () {
    $dot = generateDotArray(100);

    $us = bench(fn() => Arr::undot($dot), 1_000);

    expect($us)->toBeFloat();
});

test('bench undot 1000 keys', function () {
    $dot = generateDotArray(1_000);

    $us = bench(fn() => Arr::undot($dot), 100);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// random()
// ---------------------------------------------------------------

test('bench random count 1', function () {
    $arr = generateArray(10_000);

    $us = bench(fn() => Arr::random($arr, 1), 1_000);

    expect($us)->toBeFloat();
});

test('bench random count 100', function () {
    $arr = generateArray(10_000);

    $us = bench(fn() => Arr::random($arr, 100), 1_000);

    expect($us)->toBeFloat();
});

test('bench random full shuffle', function () {
    $arr = generateArray(10_000);

    $us = bench(fn() => Arr::random($arr, 10_000), 100);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// wrap()
// ---------------------------------------------------------------

test('bench wrap string', function () {
    $us = bench(fn() => Arr::wrap('hello'), 10_000);

    expect($us)->toBeFloat();
});

test('bench wrap array', function () {
    $arr = [1, 2, 3];

    $us = bench(fn() => Arr::wrap($arr), 10_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// keysExists()
// ---------------------------------------------------------------

test('bench keysExists 10 keys', function () {
    $arr = generateArray(1_000);
    $keys = range(0, 9);

    $us = bench(fn() => Arr::keysExists($keys, $arr), 1_000);

    expect($us)->toBeFloat();
});

test('bench keysExists 100 keys', function () {
    $arr = generateArray(1_000);
    $keys = range(0, 99);

    $us = bench(fn() => Arr::keysExists($keys, $arr), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// only()
// ---------------------------------------------------------------

test('bench only 10 keys from 10k', function () {
    $arr = generateArray(10_000);
    $keys = range(0, 9);

    $us = bench(fn() => Arr::only($arr, $keys), 1_000);

    expect($us)->toBeFloat();
});

test('bench only 100 keys from 10k', function () {
    $arr = generateArray(10_000);
    $keys = range(0, 99);

    $us = bench(fn() => Arr::only($arr, $keys), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// except()
// ---------------------------------------------------------------

test('bench except 10 keys from 10k', function () {
    $arr = generateArray(10_000);
    $keys = range(0, 9);

    $us = bench(fn() => Arr::except($arr, $keys), 1_000);

    expect($us)->toBeFloat();
});

test('bench except 100 keys from 10k', function () {
    $arr = generateArray(10_000);
    $keys = range(0, 99);

    $us = bench(fn() => Arr::except($arr, $keys), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// first() / last()
// ---------------------------------------------------------------

test('bench first 10k', function () {
    $arr = generateArray(10_000);

    $us = bench(fn() => Arr::first($arr), 10_000);

    expect($us)->toBeFloat();
});

test('bench last 10k', function () {
    $arr = generateArray(10_000);

    $us = bench(fn() => Arr::last($arr), 10_000);

    expect($us)->toBeFloat();
});
