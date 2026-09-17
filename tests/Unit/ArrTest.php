<?php

use STDW\Support\Arr;

// ---------------------------------------------------------------
// empty()
// ---------------------------------------------------------------

test('empty returns true for empty array', function () {
    expect(Arr::empty([]))->toBeTrue();
});

test('empty returns false for array with one element', function () {
    expect(Arr::empty([1]))->toBeFalse();
});

test('empty returns false for array with null value', function () {
    expect(Arr::empty([null]))->toBeFalse();
});

// ---------------------------------------------------------------
// kshift()
// ---------------------------------------------------------------

test('kshift returns first element', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    expect(Arr::kshift($arr))->toBe(['a' => 1]);
});

test('kshift removes first element from original array', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    Arr::kshift($arr);

    expect($arr)->toBe(['b' => 2, 'c' => 3]);
});

test('kshift returns null for empty array', function () {
    $arr = [];

    expect(Arr::kshift($arr))->toBeNull();
});

test('kshift works with string keys', function () {
    $arr = ['first' => 'hello', 'second' => 'world'];

    expect(Arr::kshift($arr))->toBe(['first' => 'hello']);
    expect($arr)->toBe(['second' => 'world']);
});

test('kshift works with integer keys', function () {
    $arr = [0 => 'a', 1 => 'b', 2 => 'c'];

    expect(Arr::kshift($arr))->toBe([0 => 'a']);
    expect($arr)->toBe([1 => 'b', 2 => 'c']);
});

// ---------------------------------------------------------------
// kpop()
// ---------------------------------------------------------------

test('kpop returns last element', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    expect(Arr::kpop($arr))->toBe(['c' => 3]);
});

test('kpop removes last element from original array', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    Arr::kpop($arr);

    expect($arr)->toBe(['a' => 1, 'b' => 2]);
});

test('kpop returns null for empty array', function () {
    $arr = [];

    expect(Arr::kpop($arr))->toBeNull();
});

test('kpop works with string keys', function () {
    $arr = ['first' => 'hello', 'second' => 'world'];

    expect(Arr::kpop($arr))->toBe(['second' => 'world']);
    expect($arr)->toBe(['first' => 'hello']);
});

// ---------------------------------------------------------------
// grab()
// ---------------------------------------------------------------

test('grab removes and returns value by existing key', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    expect(Arr::grab('b', $arr))->toBe(2);
    expect($arr)->toBe(['a' => 1, 'c' => 3]);
});

test('grab returns null when key does not exist', function () {
    $arr = ['a' => 1, 'b' => 2];

    expect(Arr::grab('z', $arr))->toBeNull();
    expect($arr)->toBe(['a' => 1, 'b' => 2]);
});

test('grab returns null when value is null but key exists', function () {
    $arr = ['a' => 1, 'b' => null, 'c' => 3];

    expect(Arr::grab('b', $arr))->toBeNull();
    expect($arr)->toBe(['a' => 1, 'c' => 3]);
});

test('grab works with integer keys', function () {
    $arr = [10 => 'a', 20 => 'b', 30 => 'c'];

    expect(Arr::grab(20, $arr))->toBe('b');
    expect($arr)->toBe([10 => 'a', 30 => 'c']);
});

// ---------------------------------------------------------------
// wrap()
// ---------------------------------------------------------------

test('wrap null returns empty array', function () {
    expect(Arr::wrap(null))->toBe([]);
});

test('wrap array returns same array', function () {
    $arr = [1, 2, 3];

    expect(Arr::wrap($arr))->toBe($arr);
});

test('wrap string returns array with string', function () {
    expect(Arr::wrap('hello'))->toBe(['hello']);
});

test('wrap int returns array with int', function () {
    expect(Arr::wrap(42))->toBe([42]);
});

test('wrap bool returns array with bool', function () {
    expect(Arr::wrap(true))->toBe([true]);
    expect(Arr::wrap(false))->toBe([false]);
});

// ---------------------------------------------------------------
// keysExists()
// ---------------------------------------------------------------

test('keysExists returns true when all keys exist', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    expect(Arr::keysExists(['a', 'c'], $arr))->toBeTrue();
});

test('keysExists returns false when a key is missing', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    expect(Arr::keysExists(['a', 'z'], $arr))->toBeFalse();
});

test('keysExists returns false for empty array with keys', function () {
    expect(Arr::keysExists(['a'], []))->toBeFalse();
});

test('keysExists returns true for empty keys', function () {
    $arr = ['a' => 1, 'b' => 2];

    expect(Arr::keysExists([], $arr))->toBeTrue();
});

// ---------------------------------------------------------------
// only()
// ---------------------------------------------------------------

test('only returns only requested keys', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    expect(Arr::only($arr, ['a', 'c']))->toBe(['a' => 1, 'c' => 3]);
});

test('only preserves original order', function () {
    $arr = ['c' => 3, 'a' => 1, 'b' => 2];

    expect(Arr::only($arr, ['a', 'b']))->toBe(['a' => 1, 'b' => 2]);
});

test('only ignores non-existent keys', function () {
    $arr = ['a' => 1, 'b' => 2];

    expect(Arr::only($arr, ['a', 'z']))->toBe(['a' => 1]);
});

test('only returns empty when no keys match', function () {
    $arr = ['a' => 1, 'b' => 2];

    expect(Arr::only($arr, ['x', 'y']))->toBe([]);
});

// ---------------------------------------------------------------
// except()
// ---------------------------------------------------------------

test('except excludes requested keys', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    expect(Arr::except($arr, ['b']))->toBe(['a' => 1, 'c' => 3]);
});

test('except preserves original order', function () {
    $arr = ['c' => 3, 'a' => 1, 'b' => 2];

    expect(Arr::except($arr, ['b']))->toBe(['c' => 3, 'a' => 1]);
});

test('except ignores non-existent keys', function () {
    $arr = ['a' => 1, 'b' => 2];

    expect(Arr::except($arr, ['z']))->toBe(['a' => 1, 'b' => 2]);
});

test('except returns intact array when no keys match', function () {
    $arr = ['a' => 1, 'b' => 2];

    expect(Arr::except($arr, ['x', 'y']))->toBe(['a' => 1, 'b' => 2]);
});

// ---------------------------------------------------------------
// flatten()
// ---------------------------------------------------------------

test('flatten returns same for flat array', function () {
    expect(Arr::flatten([1, 2, 3]))->toBe([1, 2, 3]);
});

test('flatten handles 2 levels nesting', function () {
    expect(Arr::flatten([1, [2, 3], 4]))->toBe([1, 2, 3, 4]);
});

test('flatten handles deep nesting', function () {
    $arr = [1, [2, [3, [4, [5]]]]];

    expect(Arr::flatten($arr))->toBe([1, 2, 3, 4, 5]);
});

test('flatten returns empty for empty array', function () {
    expect(Arr::flatten([]))->toBe([]);
});

test('flatten preserves null values', function () {
    expect(Arr::flatten([null, 1, null]))->toBe([null, 1, null]);
});

test('flatten preserves order of leaf values', function () {
    $arr = ['a', ['b', ['c', 'd']], 'e'];

    expect(Arr::flatten($arr))->toBe(['a', 'b', 'c', 'd', 'e']);
});

// ---------------------------------------------------------------
// any()
// ---------------------------------------------------------------

test('any returns true on first match', function () {
    $arr = [1, 2, 3];

    expect(Arr::any($arr, fn($v) => $v === 1))->toBeTrue();
});

test('any returns true on last match', function () {
    $arr = [1, 2, 3];

    expect(Arr::any($arr, fn($v) => $v === 3))->toBeTrue();
});

test('any returns false when no match', function () {
    $arr = [1, 2, 3];

    expect(Arr::any($arr, fn($v) => $v === 99))->toBeFalse();
});

test('any returns false for empty array', function () {
    expect(Arr::any([], fn($v) => true))->toBeFalse();
});

test('any callback receives value and key', function () {
    $arr = ['a' => 1, 'b' => 2];
    $received = [];

    Arr::any($arr, function ($v, $k) use (&$received) {
        $received[] = [$k, $v];

        return false;
    });

    expect($received[0])->toBe(['a', 1]);
    expect($received[1])->toBe(['b', 2]);
});

// ---------------------------------------------------------------
// all()
// ---------------------------------------------------------------

test('all returns true when all pass', function () {
    $arr = [2, 4, 6];

    expect(Arr::all($arr, fn($v) => $v % 2 === 0))->toBeTrue();
});

test('all returns false when none pass', function () {
    $arr = [1, 3, 5];

    expect(Arr::all($arr, fn($v) => $v % 2 === 0))->toBeFalse();
});

test('all returns true for empty array', function () {
    expect(Arr::all([], fn($v) => false))->toBeTrue();
});

test('all returns false when first fails', function () {
    $arr = [1, 2, 3];

    expect(Arr::all($arr, fn($v) => $v > 1))->toBeFalse();
});

test('all callback receives value and key', function () {
    $arr = ['x' => 10];

    Arr::all($arr, function ($v, $k) {
        expect($k)->toBe('x');
        expect($v)->toBe(10);

        return true;
    });
});

// ---------------------------------------------------------------
// find()
// ---------------------------------------------------------------

test('find returns first matching value', function () {
    $arr = [1, 2, 3, 4];

    expect(Arr::find($arr, fn($v) => $v > 2))->toBe(3);
});

test('find returns null when no match', function () {
    $arr = [1, 2, 3];

    expect(Arr::find($arr, fn($v) => $v > 10))->toBeNull();
});

test('find returns null value if it is the match', function () {
    $arr = [1, null, 3];

    expect(Arr::find($arr, fn($v) => $v === null))->toBeNull();
});

test('find callback receives value and key', function () {
    $arr = ['a' => 1, 'b' => 5];

    $result = Arr::find($arr, function ($v, $k) {
        return $k === 'b';
    });

    expect($result)->toBe(5);
});

// ---------------------------------------------------------------
// findKey()
// ---------------------------------------------------------------

test('findKey returns key of first matching value', function () {
    $arr = ['a' => 1, 'b' => 5, 'c' => 3];

    expect(Arr::findKey($arr, fn($v) => $v > 3))->toBe('b');
});

test('findKey returns null when no match', function () {
    $arr = ['a' => 1, 'b' => 2];

    expect(Arr::findKey($arr, fn($v) => $v > 10))->toBeNull();
});

test('findKey callback receives value and key', function () {
    $arr = [10 => 'x', 20 => 'y'];

    $result = Arr::findKey($arr, function ($v, $k) {
        return $k === 20;
    });

    expect($result)->toBe(20);
});

// ---------------------------------------------------------------
// first()
// ---------------------------------------------------------------

test('first returns first value', function () {
    $arr = [10, 20, 30];

    expect(Arr::first($arr))->toBe(10);
});

test('first returns null for empty array', function () {
    expect(Arr::first([]))->toBeNull();
});

test('first returns value for single element array', function () {
    expect(Arr::first([42]))->toBe(42);
});

// ---------------------------------------------------------------
// last()
// ---------------------------------------------------------------

test('last returns last value', function () {
    $arr = [10, 20, 30];

    expect(Arr::last($arr))->toBe(30);
});

test('last returns null for empty array', function () {
    expect(Arr::last([]))->toBeNull();
});

test('last returns value for single element array', function () {
    expect(Arr::last([42]))->toBe(42);
});

// ---------------------------------------------------------------
// dot()
// ---------------------------------------------------------------

test('dot flattens 2 levels', function () {
    $arr = ['a' => ['b' => 1]];

    expect(Arr::dot($arr))->toBe(['a.b' => 1]);
});

test('dot flattens 3+ levels', function () {
    $arr = ['a' => ['b' => ['c' => 1]]];

    expect(Arr::dot($arr))->toBe(['a.b.c' => 1]);
});

test('dot returns empty for empty array', function () {
    expect(Arr::dot([]))->toBe([]);
});

test('dot converts numeric keys to string', function () {
    $arr = [0 => ['a' => 1]];

    expect(Arr::dot($arr))->toBe(['0.a' => 1]);
});

test('dot preserves value types', function () {
    $arr = ['config' => ['name' => 'test', 'count' => 5, 'active' => true, 'data' => null]];

    expect(Arr::dot($arr))->toBe([
        'config.name' => 'test',
        'config.count' => 5,
        'config.active' => true,
        'config.data' => null,
    ]);
});

// ---------------------------------------------------------------
// undot()
// ---------------------------------------------------------------

test('undot reverts dot notation', function () {
    $dot = ['a.b.c' => 1];

    expect(Arr::undot($dot))->toBe(['a' => ['b' => ['c' => 1]]]);
});

test('undot handles deep nesting', function () {
    $dot = ['a.b.c.d.e' => 'value'];

    expect(Arr::undot($dot))->toBe(['a' => ['b' => ['c' => ['d' => ['e' => 'value']]]]]);
});

test('undot overwrites duplicate keys (last wins)', function () {
    $dot = ['a.b' => 1, 'a.b' => 2];

    expect(Arr::undot($dot))->toBe(['a' => ['b' => 2]]);
});

test('undot returns empty for empty array', function () {
    expect(Arr::undot([]))->toBe([]);
});

test('undot preserves value types', function () {
    $dot = [
        'config.name' => 'test',
        'config.count' => 5,
        'config.active' => true,
        'config.data' => null,
    ];

    $result = Arr::undot($dot);

    expect($result)->toBe([
        'config' => [
            'name' => 'test',
            'count' => 5,
            'active' => true,
            'data' => null,
        ],
    ]);
});

// ---------------------------------------------------------------
// random()
// ---------------------------------------------------------------

test('random returns empty for empty array', function () {
    expect(Arr::random([]))->toBe([]);
});

test('random returns array with 1 element by default', function () {
    $arr = [1, 2, 3];

    $result = Arr::random($arr);

    expect($result)->toBeArray()->toHaveCount(1);
});

test('random with count > size returns all elements', function () {
    $arr = [1, 2, 3];

    $result = Arr::random($arr, 10);

    expect($result)->toBeArray()->toHaveCount(3);
    expect(array_values($result))->toEqualCanonicalizing([1, 2, 3]);
});

test('random with count equal to size returns all elements', function () {
    $arr = [1, 2, 3];

    $result = Arr::random($arr, 3);

    expect($result)->toBeArray()->toHaveCount(3);
    expect(array_values($result))->toEqualCanonicalizing([1, 2, 3]);
});

test('random preserves original keys', function () {
    $arr = ['a' => 1, 'b' => 2, 'c' => 3];

    $result = Arr::random($arr, 2);

    foreach ($result as $key => $value) {
        expect($arr)->toHaveKey($key);
        expect($arr[$key])->toBe($value);
    }
});

test('random works with associative arrays', function () {
    $arr = ['name' => 'John', 'age' => 30, 'city' => 'NYC'];

    $result = Arr::random($arr, 1);

    expect($result)->toBeArray()->toHaveCount(1);

    $key = array_key_first($result);

    expect(array_key_exists($key, $arr))->toBeTrue();
});
