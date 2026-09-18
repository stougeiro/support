<?php

use STDW\Support\Date;

/*
|--------------------------------------------------------------------------
| Performance Benchmarks for Date
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
// isValidDate()
// ---------------------------------------------------------------

test('bench isValidDate valid', function () {
    $us = bench(fn() => Date::isValidDate('2024-12-31'), 1_000);

    expect($us)->toBeFloat();
});

test('bench isValidDate invalid regex', function () {
    $us = bench(fn() => Date::isValidDate('not-a-date'), 1_000);

    expect($us)->toBeFloat();
});

test('bench isValidDate invalid checkdate (feb 30)', function () {
    $us = bench(fn() => Date::isValidDate('2024-02-30'), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// isValidTime()
// ---------------------------------------------------------------

test('bench isValidTime valid HH:MM:SS', function () {
    $us = bench(fn() => Date::isValidTime('23:59:59'), 1_000);

    expect($us)->toBeFloat();
});

test('bench isValidTime invalid', function () {
    $us = bench(fn() => Date::isValidTime('25:00'), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// isValidDateTime()
// ---------------------------------------------------------------

test('bench isValidDateTime valid with T', function () {
    $us = bench(fn() => Date::isValidDateTime('2024-12-31T23:59:59'), 1_000);

    expect($us)->toBeFloat();
});

test('bench isValidDateTime invalid', function () {
    $us = bench(fn() => Date::isValidDateTime('2024-12-31 25:00:00'), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// convert()
// ---------------------------------------------------------------

test('bench convert Y-m-d to d/m/Y', function () {
    $us = bench(fn() => Date::convert('2024-12-31', 'Y-m-d', 'd/m/Y'), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// diff()
// ---------------------------------------------------------------

test('bench diff 30 days apart', function () {
    $us = bench(fn() => Date::diff('2024-12-01', '2024-12-31'), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// ago()
// ---------------------------------------------------------------

test('bench ago past date', function () {
    $past = date('Y-m-d', strtotime('-30 days'));

    $us = bench(fn() => Date::ago($past), 1_000);

    expect($us)->toBeFloat();
});

test('bench ago with custom labels', function () {
    $past = date('Y-m-d H:i:s', time() - 7200);
    $labels = ['hours' => 'horas', 'ago' => 'atrás'];

    $us = bench(fn() => Date::ago($past, 'Y-m-d H:i:s', $labels), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// fromNow()
// ---------------------------------------------------------------

test('bench fromNow future date', function () {
    $future = date('Y-m-d', strtotime('+30 days'));

    $us = bench(fn() => Date::fromNow($future), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// withTimezone()
// ---------------------------------------------------------------

test('bench withTimezone closure overhead', function () {
    $us = bench(fn() => Date::withTimezone('UTC', fn () => null), 1_000);

    expect($us)->toBeFloat();
});
