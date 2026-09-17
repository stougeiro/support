<?php

use STDW\Support\Arr;

// ---------------------------------------------------------------
// dot() <-> undot() roundtrip
// ---------------------------------------------------------------

test('dot and undot roundtrip preserves structure', function () {
    $arr = ['a' => ['b' => ['c' => 1]], 'd' => ['e' => 2]];

    expect(Arr::undot(Arr::dot($arr)))->toBe($arr);
});

test('dot and undot roundtrip preserves types', function () {
    $arr = [
        'string' => 'hello',
        'int' => 42,
        'bool' => true,
        'null' => null,
        'nested' => ['deep' => 'value'],
    ];

    $result = Arr::undot(Arr::dot($arr));

    expect($result['string'])->toBe('hello');
    expect($result['int'])->toBe(42);
    expect($result['bool'])->toBeTrue();
    expect($result['null'])->toBeNull();
    expect($result['nested']['deep'])->toBe('value');
});

// ---------------------------------------------------------------
// only() + except() complementar
// ---------------------------------------------------------------

test('only and except are complementary', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];
    $keys = ['a', 'c'];

    $only = Arr::only($arr, $keys);
    $except = Arr::except($arr, $keys);

    // Together they contain all original key-value pairs
    $merged = $only + $except;

    foreach ($arr as $key => $value) {
        expect($merged)->toHaveKey($key);
        expect($merged[$key])->toBe($value);
    }

    // No overlap between only and except
    expect(array_intersect_key($only, $except))->toBe([]);
    // Total count matches
    expect(count($only) + count($except))->toBe(count($arr));
});

test('only and except preserve order', function () {
    $arr = ['c' => 3, 'a' => 1, 'b' => 2];
    $keys = ['a'];

    $only = Arr::only($arr, $keys);
    $except = Arr::except($arr, $keys);

    // only has 'a', except has 'c' and 'b' in original order
    expect(array_keys($only))->toBe(['a']);
    expect(array_keys($except))->toBe(['c', 'b']);
});

// ---------------------------------------------------------------
// kshift drain
// ---------------------------------------------------------------

test('kshift drain returns all elements in order', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];
    $drained = [];

    while (($item = Arr::kshift($arr)) !== null) {
        $drained[] = $item;
    }

    expect($drained)->toBe([
        ['a' => 1],
        ['b' => 2],
        ['c' => 3],
    ]);
    expect($arr)->toBe([]);
});

// ---------------------------------------------------------------
// kpop drain
// ---------------------------------------------------------------

test('kpop drain returns all elements in reverse order', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];
    $drained = [];

    while (($item = Arr::kpop($arr)) !== null) {
        $drained[] = $item;
    }

    expect($drained)->toBe([
        ['c' => 3],
        ['b' => 2],
        ['a' => 1],
    ]);
    expect($arr)->toBe([]);
});

// ---------------------------------------------------------------
// any() + all() complementar
// ---------------------------------------------------------------

test('any and all are logically complementary', function () {
    $arr = [1, 2, 3, 4, 5];
    $cb = fn($v) => $v > 3;

    $anyResult = Arr::any($arr, $cb);
    $allNotResult = Arr::all($arr, fn($v) => !$cb($v));

    expect($anyResult)->toBe(!$allNotResult);
});

// ---------------------------------------------------------------
// find() + findKey() consistencia
// ---------------------------------------------------------------

test('findKey returns key of find result', function () {
    $arr = ['a' => 1, 'b' => 5, 'c' => 3];
    $cb = fn($v) => $v > 2;

    $value = Arr::find($arr, $cb);
    $key = Arr::findKey($arr, $cb);

    expect($arr[$key])->toBe($value);
});

// ---------------------------------------------------------------
// first() + last() com 1 elemento
// ---------------------------------------------------------------

test('first and last return same value for single element', function () {
    $arr = [42];

    expect(Arr::first($arr))->toBe(Arr::last($arr));
    expect(Arr::first($arr))->toBe(42);
});

// ---------------------------------------------------------------
// random() full
// ---------------------------------------------------------------

test('random with full count contains all elements', function () {
    $arr = ['x' => 10, 'y' => 20, 'z' => 30];

    $result = Arr::random($arr, count($arr));

    expect(array_keys($result))->toEqualCanonicalizing(array_keys($arr));
    expect(array_values($result))->toEqualCanonicalizing(array_values($arr));
});
