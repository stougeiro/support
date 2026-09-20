<?php

use STDW\Support\Str;

// ---------------------------------------------------------------
// Scenario: slugify with transliteration fallback
// ---------------------------------------------------------------

test('slugify handles full portuguese title', function () {
    $title = 'São Paulo: A Capital do Estado!';
    $slug = Str::slugify($title);

    expect($slug)->toBe('sao-paulo-a-capital-do-estado');
    expect($slug)->not->toContain(' ');
    expect($slug)->not->toMatch('/[A-Z]/');
});

test('slugify handles mixed accents and special chars', function () {
    $text = 'Café & Croissant — Très Bon!';
    $slug = Str::slugify($text);

    expect($slug)->toBe('cafe-croissant-tres-bon');
});

// ---------------------------------------------------------------
// Scenario: mask + onlyNumbers pipeline for phone
// ---------------------------------------------------------------

test('phone formatting pipeline', function () {
    $input = '(11) 99999-1234';
    $digits = Str::onlyNumbers($input);
    $formatted = Str::mask('(##) #####-####', $digits);

    expect($digits)->toBe('11999991234');
    expect($formatted)->toBe('(11) 99999-1234');
});

// ---------------------------------------------------------------
// Scenario: mask + onlyNumbers pipeline for CPF
// ---------------------------------------------------------------

test('CPF formatting pipeline', function () {
    $input = '12345678901';
    $formatted = Str::mask('###.###.###-##', $input);

    expect($formatted)->toBe('123.456.789-01');
});

// ---------------------------------------------------------------
// Scenario: empty + ttrim validation pipeline
// ---------------------------------------------------------------

test('ttrim normalizes then empty validates', function () {
    $dirty = '   ';
    $clean = Str::ttrim($dirty);

    expect(Str::empty($clean))->toBeTrue();
    expect($clean)->toBe('');
});

test('ttrim preserves content then empty returns false', function () {
    $dirty = '  hello  ';
    $clean = Str::ttrim($dirty);

    expect(Str::empty($clean))->toBeFalse();
    expect($clean)->toBe('hello');
});

// ---------------------------------------------------------------
// Scenario: isFqcn vs slugify separation
// ---------------------------------------------------------------

test('valid FQCN is not a valid slug', function () {
    $fqcn = 'App\Models\User';

    expect(Str::isFqcn($fqcn))->toBeTrue();
    expect(Str::slugify($fqcn))->not->toBe($fqcn);
});

test('slug is not a valid FQCN', function () {
    $slug = Str::slugify('Hello World');

    expect($slug)->toBe('hello-world');
    expect(Str::isFqcn($slug))->toBeFalse();
});
