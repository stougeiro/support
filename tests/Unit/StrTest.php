<?php

use STDW\Support\Str;

// ---------------------------------------------------------------
// empty()
// ---------------------------------------------------------------

test('empty returns true for null', function () {
    expect(Str::empty(null))->toBeTrue();
});

test('empty returns true for empty string', function () {
    expect(Str::empty(''))->toBeTrue();
});

test('empty returns true for whitespace only', function () {
    expect(Str::empty('   '))->toBeTrue();
});

test('empty returns true for tabs and newlines', function () {
    $input = chr(9) . chr(10) . chr(13);

    expect(Str::empty($input))->toBeTrue();
});

test('empty returns false for non-empty string', function () {
    expect(Str::empty('hello'))->toBeFalse();
});

test('empty returns false for zero string', function () {
    expect(Str::empty('0'))->toBeFalse();
});

// ---------------------------------------------------------------
// ttrim()
// ---------------------------------------------------------------

test('ttrim removes leading and trailing spaces', function () {
    expect(Str::ttrim('  hello  '))->toBe('hello');
});

test('ttrim collapses multiple internal spaces', function () {
    expect(Str::ttrim('a  b  c'))->toBe('a b c');
});

test('ttrim removes tabs and newlines', function () {
    $input = 'a' . chr(9) . 'b' . chr(10) . 'c';

    expect(Str::ttrim($input))->toBe('a b c');
});

test('ttrim returns empty for whitespace only', function () {
    expect(Str::ttrim('   '))->toBe('');
});

test('ttrim returns unchanged when already clean', function () {
    expect(Str::ttrim('hello world'))->toBe('hello world');
});

// ---------------------------------------------------------------
// onlyNumbers()
// ---------------------------------------------------------------

test('onlyNumbers returns empty for null', function () {
    expect(Str::onlyNumbers(null))->toBe('');
});

test('onlyNumbers returns empty for non-digit string', function () {
    expect(Str::onlyNumbers('abcdef'))->toBe('');
});

test('onlyNumbers extracts only digits', function () {
    expect(Str::onlyNumbers('abc123def456'))->toBe('123456');
});

test('onlyNumbers handles phone with mask', function () {
    expect(Str::onlyNumbers('(11) 99999-1234'))->toBe('11999991234');
});

test('onlyNumbers preserves all digits', function () {
    expect(Str::onlyNumbers('1234567890'))->toBe('1234567890');
});

// ---------------------------------------------------------------
// mask()
// ---------------------------------------------------------------

test('mask applies template correctly', function () {
    expect(Str::mask('###.###.###-##', '12345678901'))->toBe('123.456.789-01');
});

test('mask returns null when value shorter than template', function () {
    expect(Str::mask('#####', 'ab'))->toBeNull();
});

test('mask works with custom character', function () {
    expect(Str::mask('@@-@@', 'ABCD', '@'))->toBe('AB-CD');
});

test('mask truncates when value longer than template', function () {
    $result = Str::mask('##-##', '123456');

    expect($result)->toBe('12-34');
});

test('mask handles multibyte characters', function () {
    expect(Str::mask('###', 'Ola'))->toBe('Ola');
});

test('mask handles multibyte with more slots', function () {
    expect(Str::mask('######', 'Ola Mundo'))->toBe('Ola Mu');
});

// ---------------------------------------------------------------
// slugify()
// ---------------------------------------------------------------

test('slugify converts spaces to hyphens', function () {
    expect(Str::slugify('hello world'))->toBe('hello-world');
});

test('slugify removes accents', function () {
    expect(Str::slugify('Ola Mundo'))->toBe('ola-mundo');
});

test('slugify converts to lowercase', function () {
    expect(Str::slugify('HELLO WORLD'))->toBe('hello-world');
});

test('slugify removes duplicate hyphens', function () {
    expect(Str::slugify('a  b  c'))->toBe('a-b-c');
});

test('slugify trims hyphens from edges', function () {
    expect(Str::slugify(' hello '))->toBe('hello');
});

test('slugify returns empty for empty string', function () {
    expect(Str::slugify(''))->toBe('');
});

test('slugify handles special characters', function () {
    expect(Str::slugify('Ola, Mundo!'))->toBe('ola-mundo');
});

test('slugify handles portuguese accents', function () {
    expect(Str::slugify('Sao Paulo'))->toBe('sao-paulo');
});

test('slugify handles german umlauts', function () {
    expect(Str::slugify('Munchen'))->toBe('munchen');
});

// ---------------------------------------------------------------
// isFqcn()
// ---------------------------------------------------------------

test('isFqcn returns true for valid multi-part', function () {
    expect(Str::isFqcn('App\Models\User'))->toBeTrue();
});

test('isFqcn returns true for single part', function () {
    expect(Str::isFqcn('User'))->toBeTrue();
});

test('isFqcn returns true with leading backslash', function () {
    expect(Str::isFqcn('\App\Models\User'))->toBeTrue();
});

test('isFqcn returns false for empty string', function () {
    expect(Str::isFqcn(''))->toBeFalse();
});

test('isFqcn returns false for lowercase start', function () {
    expect(Str::isFqcn('app\Models\User'))->toBeFalse();
});

test('isFqcn returns false for empty segment', function () {
    expect(Str::isFqcn('App\\\\User'))->toBeFalse();
});

test('isFqcn returns false for spaces', function () {
    expect(Str::isFqcn('App Models'))->toBeFalse();
});

test('isFqcn returns false for special characters', function () {
    expect(Str::isFqcn('App\\Models\\User-Name'))->toBeFalse();
});
