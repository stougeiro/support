<?php

use STDW\Support\Base64;

// ---------------------------------------------------------------
// encode()
// ---------------------------------------------------------------

test('encode returns URL-safe string', function () {
    $encoded = Base64::encode('hello world');

    expect($encoded)->not->toContain('+');
    expect($encoded)->not->toContain('/');
    expect($encoded)->not->toContain('=');
});

test('encode replaces plus with dash', function () {
    $encoded = Base64::encode("\xfb");

    expect($encoded)->toContain('-');
    expect($encoded)->not->toContain('+');
});

test('encode replaces slash with underscore', function () {
    $encoded = Base64::encode("\xff");

    expect($encoded)->toContain('_');
    expect($encoded)->not->toContain('/');
});

test('encode removes padding', function () {
    $encoded = Base64::encode('a');

    expect($encoded)->not->toContain('=');
    expect(strlen($encoded))->toBe(2);
});

test('encode handles binary data', function () {
    $binary = "\x00\x01\x02\x03\x04\x05";
    $encoded = Base64::encode($binary);

    expect($encoded)->not->toContain('+');
    expect($encoded)->not->toContain('/');
    expect(Base64::decode($encoded))->toBe($binary);
});

test('encode returns empty for empty string', function () {
    expect(Base64::encode(''))->toBe('');
});

// ---------------------------------------------------------------
// decode()
// ---------------------------------------------------------------

test('decode returns original string via roundtrip', function () {
    $original = 'hello world';
    $encoded = Base64::encode($original);

    expect(Base64::decode($encoded))->toBe($original);
});

test('decode handles URL-safe dash and underscore', function () {
    $encoded = Base64::encode("\xfb\xfc\xfd");

    expect($encoded)->toContain('-');
    expect($encoded)->toContain('_');

    $decoded = Base64::decode($encoded);

    expect($decoded)->not->toBeFalse();
    expect($decoded)->toBe("\xfb\xfc\xfd");
});

test('decode returns false for invalid input', function () {
    expect(Base64::decode('!!!invalid!!!'))->toBeFalse();
});

test('decode returns empty for empty string', function () {
    expect(Base64::decode(''))->toBe('');
});

test('roundtrip with special characters', function () {
    $original = 'hello+world/with=special';
    $encoded = Base64::encode($original);

    expect(Base64::decode($encoded))->toBe($original);
});

test('roundtrip with binary data', function () {
    $binary = '';
    for ($i = 0; $i < 256; $i++) {
        $binary .= chr($i);
    }

    $encoded = Base64::encode($binary);

    expect(Base64::decode($encoded))->toBe($binary);
});
