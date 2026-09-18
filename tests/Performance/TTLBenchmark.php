<?php

use STDW\Support\TTL;

/*
|--------------------------------------------------------------------------
| Performance Benchmarks for TTL
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
// none()
// ---------------------------------------------------------------

test('bench none', function () {
    $us = bench(fn() => TTL::none(), 100_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// seconds()
// ---------------------------------------------------------------

test('bench seconds', function () {
    $us = bench(fn() => TTL::seconds(30), 100_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// minutes()
// ---------------------------------------------------------------

test('bench minutes', function () {
    $us = bench(fn() => TTL::minutes(5), 100_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// hours()
// ---------------------------------------------------------------

test('bench hours', function () {
    $us = bench(fn() => TTL::hours(2), 100_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// days()
// ---------------------------------------------------------------

test('bench days', function () {
    $us = bench(fn() => TTL::days(7), 100_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// weeks()
// ---------------------------------------------------------------

test('bench weeks', function () {
    $us = bench(fn() => TTL::weeks(4), 100_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// months()
// ---------------------------------------------------------------

test('bench months', function () {
    $us = bench(fn() => TTL::months(6), 100_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// years()
// ---------------------------------------------------------------

test('bench years', function () {
    $us = bench(fn() => TTL::years(1), 100_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// forever()
// ---------------------------------------------------------------

test('bench forever', function () {
    $us = bench(fn() => TTL::forever(), 100_000);

    expect($us)->toBeFloat();
});
