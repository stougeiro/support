<?php

use STDW\Support\TTL;

// ---------------------------------------------------------------
// Scenario: cache TTL configuration
// ---------------------------------------------------------------

test('cache TTL scenario', function () {
    $tokenTTL = TTL::minutes(30);
    $sessionTTL = TTL::days(7);
    $apiKeyTTL = TTL::years(1);

    expect($tokenTTL)->toBe(1800);
    expect($sessionTTL)->toBe(604800);
    expect($apiKeyTTL)->toBe(31536000);
});

// ---------------------------------------------------------------
// Scenario: negative values normalize to zero
// ---------------------------------------------------------------

test('negative values normalize to zero', function () {
    expect(TTL::seconds(-5))->toBe(0);
    expect(TTL::minutes(-5))->toBe(0);
    expect(TTL::hours(-5))->toBe(0);
    expect(TTL::days(-5))->toBe(0);
    expect(TTL::weeks(-5))->toBe(0);
    expect(TTL::months(-5))->toBe(0);
    expect(TTL::years(-5))->toBe(0);
});

// ---------------------------------------------------------------
// Scenario: chained TTL calculation
// ---------------------------------------------------------------

test('chained TTL calculation', function () {
    $ttl = TTL::minutes(5) + TTL::seconds(30);

    expect($ttl)->toBe(330);
});
