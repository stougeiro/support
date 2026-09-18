<?php

use STDW\Support\Password;

// ---------------------------------------------------------------
// Scenario: login with hash upgrade
// ---------------------------------------------------------------

test('login scenario with hash upgrade', function () {
    $password = 'MyS3cureP@ss!';

    $oldHash = Password::hash($password, PASSWORD_BCRYPT, 8);

    expect(Password::verify($password, $oldHash))->toBeTrue();

    $newHash = Password::upgrade($password, $oldHash, PASSWORD_BCRYPT, 12);

    expect($newHash)->not->toBeNull();
    expect(Password::verify($password, $newHash))->toBeTrue();
});

// ---------------------------------------------------------------
// Scenario: algorithm migration bcrypt to argon2id
// ---------------------------------------------------------------

test('algorithm migration bcrypt to argon2id', function () {
    if ( ! defined('PASSWORD_ARGON2ID')) {
        $this->markTestSkipped('Argon2ID not supported');
    }

    $password = 'MyS3cureP@ss!';

    $oldHash = Password::hash($password, PASSWORD_BCRYPT, 12);

    expect(Password::algo($oldHash))->toBe('bcrypt');

    $newHash = Password::upgrade($password, $oldHash, PASSWORD_ARGON2ID);

    expect($newHash)->not->toBeNull();
    expect(Password::algo($newHash))->toBe('argon2id');
    expect(Password::verify($password, $newHash))->toBeTrue();
});

// ---------------------------------------------------------------
// Scenario: password policy enforcement
// ---------------------------------------------------------------

test('password policy enforcement', function () {
    $policy = [
        'min' => 8,
        'uppercase' => true,
        'lowercase' => true,
        'numbers' => true,
        'symbols' => true,
    ];

    expect(Password::validate('Abc1!efg', $policy))->toBeTrue();
    expect(Password::validate('abc123', $policy))->toBeFalse();
    expect(Password::validate('ABC123!@', $policy))->toBeFalse();
    expect(Password::validate('abcdefgh', $policy))->toBeFalse();
});
