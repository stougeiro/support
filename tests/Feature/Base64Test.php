<?php

use STDW\Support\Base64;

// ---------------------------------------------------------------
// Scenario: token-like encode/decode
// ---------------------------------------------------------------

test('token-like roundtrip', function () {
    $token = 'eyJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoxMjN9';

    $encoded = Base64::encode($token);

    expect($encoded)->not->toBe($token);

    $decoded = Base64::decode($encoded);

    expect($decoded)->toBe($token);
});

// ---------------------------------------------------------------
// Scenario: multi-byte UTF-8 roundtrip
// ---------------------------------------------------------------

test('multi-byte UTF-8 roundtrip', function () {
    $utf8 = 'Olá mundo! 你好 🎉';

    $encoded = Base64::encode($utf8);

    expect(Base64::decode($encoded))->toBe($utf8);
});

// ---------------------------------------------------------------
// Scenario: URL parameter usage
// ---------------------------------------------------------------

test('URL parameter roundtrip', function () {
    $value = 'redirect=https://example.com/path?q=1&b=2';
    $encoded = Base64::encode($value);

    expect($encoded)->not->toContain('+');
    expect($encoded)->not->toContain('/');
    expect($encoded)->not->toContain('=');

    $url = 'https://api.example.com/callback?token=' . $encoded;

    expect(str_contains($url, '?token='))->toBeTrue();

    $decoded = Base64::decode($encoded);

    expect($decoded)->toBe($value);
});
