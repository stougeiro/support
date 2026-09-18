<?php

use STDW\Support\Base64;

/*
|--------------------------------------------------------------------------
| Performance Benchmarks for Base64
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

// ---------------------------------------------------------------
// encode()
// ---------------------------------------------------------------

test('bench encode ASCII', function () {
    $us = bench(fn() => Base64::encode('Hello, World! This is a test string for benchmarking.'), 10_000);

    expect($us)->toBeFloat();
});

test('bench encode binary', function () {
    $binary = random_bytes(256);

    $us = bench(fn() => Base64::encode($binary), 10_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// decode()
// ---------------------------------------------------------------

test('bench decode ASCII', function () {
    $encoded = Base64::encode('Hello, World! This is a test string for benchmarking.');

    $us = bench(fn() => Base64::decode($encoded), 10_000);

    expect($us)->toBeFloat();
});

test('bench decode binary', function () {
    $encoded = Base64::encode(random_bytes(256));

    $us = bench(fn() => Base64::decode($encoded), 10_000);

    expect($us)->toBeFloat();
});
