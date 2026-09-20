<?php

use STDW\Support\Str;

/*
|--------------------------------------------------------------------------
| Performance Benchmarks for Str
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
// empty()
// ---------------------------------------------------------------

test('bench empty short string', function () {
    $us = bench(fn() => Str::empty('hello'), 10_000);

    expect($us)->toBeFloat();
});

test('bench empty long string', function () {
    $text = str_repeat('a', 10_000);

    $us = bench(fn() => Str::empty($text), 10_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// ttrim()
// ---------------------------------------------------------------

test('bench ttrim clean string', function () {
    $us = bench(fn() => Str::ttrim('hello world'), 10_000);

    expect($us)->toBeFloat();
});

test('bench ttrim with excess whitespace', function () {
    $text = '  h e l l o   w o r l d  ';

    $us = bench(fn() => Str::ttrim($text), 10_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// onlyNumbers()
// ---------------------------------------------------------------

test('bench onlyNumbers short string', function () {
    $us = bench(fn() => Str::onlyNumbers('abc123def456'), 10_000);

    expect($us)->toBeFloat();
});

test('bench onlyNumbers phone format', function () {
    $us = bench(fn() => Str::onlyNumbers('(11) 99999-1234'), 10_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// mask()
// ---------------------------------------------------------------

test('bench mask phone', function () {
    $us = bench(fn() => Str::mask('(##) #####-####', '11999991234'), 10_000);

    expect($us)->toBeFloat();
});

test('bench mask exact value', function () {
    $us = bench(fn() => Str::mask('##-##', 'ABCD'), 10_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// slugify()
// ---------------------------------------------------------------

test('bench slugify ASCII only', function () {
    $us = bench(fn() => Str::slugify('Hello World This Is A Test'), 10_000);

    expect($us)->toBeFloat();
});

test('bench slugify with accents', function () {
    $us = bench(fn() => Str::slugify('São Paulo: A Capital do Estado!'), 10_000);

    expect($us)->toBeFloat();
});

test('bench slugify long string', function () {
    $text = str_repeat('Olá Mundo! ', 100);

    $us = bench(fn() => Str::slugify($text), 1_000);

    expect($us)->toBeFloat();
});

// ---------------------------------------------------------------
// isFqcn()
// ---------------------------------------------------------------

test('bench isFqcn valid short', function () {
    $us = bench(fn() => Str::isFqcn('App\Models\User'), 10_000);

    expect($us)->toBeFloat();
});

test('bench isFqcn valid long', function () {
    $fqcn = 'App\\Models\\User\\Profile\\Address\\City';

    $us = bench(fn() => Str::isFqcn($fqcn), 10_000);

    expect($us)->toBeFloat();
});

test('bench isFqcn invalid', function () {
    $us = bench(fn() => Str::isFqcn('not-a-class'), 10_000);

    expect($us)->toBeFloat();
});
