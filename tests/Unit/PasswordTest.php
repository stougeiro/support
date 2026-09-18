<?php

use STDW\Support\Password;

// ---------------------------------------------------------------
// hash()
// ---------------------------------------------------------------

test('hash creates bcrypt hash by default', function () {
    $hash = Password::hash('secret');

    expect($hash)->not->toBe('secret');
    expect(str_starts_with($hash, '$2y$'))->toBeTrue();
});

test('hash creates argon2id hash', function () {
    if ( ! defined('PASSWORD_ARGON2ID')) {
        $this->markTestSkipped('Argon2ID not supported');
    }

    $hash = Password::hash('secret', PASSWORD_ARGON2ID);

    expect(str_starts_with($hash, '$argon2id$'))->toBeTrue();
});

test('hash with custom cost', function () {
    $hash = Password::hash('secret', PASSWORD_BCRYPT, 8);

    expect(str_starts_with($hash, '$2y$'))->toBeTrue();
});

test('hash produces different hashes for same password', function () {
    $hash1 = Password::hash('secret');
    $hash2 = Password::hash('secret');

    expect($hash1)->not->toBe($hash2);
});

// ---------------------------------------------------------------
// verify()
// ---------------------------------------------------------------

test('verify returns true for correct password', function () {
    $hash = Password::hash('secret');

    expect(Password::verify('secret', $hash))->toBeTrue();
});

test('verify returns false for wrong password', function () {
    $hash = Password::hash('secret');

    expect(Password::verify('wrong', $hash))->toBeFalse();
});

// ---------------------------------------------------------------
// rehash()
// ---------------------------------------------------------------

test('needsRehash returns true for different cost', function () {
    $hash = Password::hash('secret', PASSWORD_BCRYPT, 8);

    expect(Password::needsRehash($hash, PASSWORD_BCRYPT, 12))->toBeTrue();
});

test('needsRehash returns false for same cost', function () {
    $hash = Password::hash('secret', PASSWORD_BCRYPT, 12);

    expect(Password::needsRehash($hash, PASSWORD_BCRYPT, 12))->toBeFalse();
});

// ---------------------------------------------------------------
// upgrade()
// ---------------------------------------------------------------

test('upgrade returns new hash when needed', function () {
    $hash = Password::hash('secret', PASSWORD_BCRYPT, 8);

    $newHash = Password::upgrade('secret', $hash, PASSWORD_BCRYPT, 12);

    expect($newHash)->not->toBeNull();
    expect($newHash)->not->toBe($hash);
    expect(Password::verify('secret', $newHash))->toBeTrue();
});

test('upgrade returns null when already up to date', function () {
    $hash = Password::hash('secret', PASSWORD_BCRYPT, 12);

    $newHash = Password::upgrade('secret', $hash, PASSWORD_BCRYPT, 12);

    expect($newHash)->toBeNull();
});

test('upgrade returns null for wrong password', function () {
    $hash = Password::hash('secret', PASSWORD_BCRYPT, 8);

    $newHash = Password::upgrade('wrong', $hash, PASSWORD_BCRYPT, 12);

    expect($newHash)->toBeNull();
});

// ---------------------------------------------------------------
// algo()
// ---------------------------------------------------------------

test('algo detects bcrypt', function () {
    $hash = Password::hash('secret', PASSWORD_BCRYPT);

    expect(Password::algo($hash))->toBe('bcrypt');
});

test('algo detects argon2id', function () {
    if ( ! defined('PASSWORD_ARGON2ID')) {
        $this->markTestSkipped('Argon2ID not supported');
    }

    $hash = Password::hash('secret', PASSWORD_ARGON2ID);

    expect(Password::algo($hash))->toBe('argon2id');
});

test('algo returns unknown for invalid hash', function () {
    expect(Password::algo('invalid'))->toBe('unknown');
});

// ---------------------------------------------------------------
// strength()
// ---------------------------------------------------------------

test('strength returns 0 for empty string', function () {
    expect(Password::strength(''))->toBe(0);
});

test('strength returns 0 when min fails', function () {
    expect(Password::strength('abc'))->toBe(0);
});

test('strength returns 1 for lowercase only', function () {
    expect(Password::strength('abcdefgh'))->toBe(1);
});

test('strength returns 2 for mixed case', function () {
    expect(Password::strength('Abcdefgh'))->toBe(2);
});

test('strength returns 3 with numbers', function () {
    expect(Password::strength('Abcdefg1'))->toBe(3);
});

test('strength returns 4 for strong password', function () {
    expect(Password::strength('Abcdefg1!'))->toBe(4);
});

test('strength caps at 4', function () {
    expect(Password::strength('Aa1!Aa1!Aa1!Aa1!'))->toBe(4);
});

// ---------------------------------------------------------------
// strength() with rules
// ---------------------------------------------------------------

test('strength with custom rules overrides default', function () {
    expect(Password::strength('abc', ['min' => 3]))->toBe(1);
});

test('strength with partial rules uses default for the rest', function () {
    $rules = ['min' => 8, 'uppercase' => true];

    expect(Password::strength('Abcdefgh', $rules))->toBe(2);
});

test('strength with all rules specified', function () {
    $rules = ['min' => 8, 'lowercase' => true, 'uppercase' => true, 'numbers' => true, 'symbols' => true];

    expect(Password::strength('Abcdefg1!', $rules))->toBe(4);
});

// ---------------------------------------------------------------
// strengthLabel()
// ---------------------------------------------------------------

test('strengthLabel returns default labels', function () {
    expect(Password::strengthLabel(0))->toBe('Very Weak');
    expect(Password::strengthLabel(1))->toBe('Weak');
    expect(Password::strengthLabel(2))->toBe('Fair');
    expect(Password::strengthLabel(3))->toBe('Strong');
    expect(Password::strengthLabel(4))->toBe('Very Strong');
});

test('strengthLabel with custom labels overrides default', function () {
    $labels = [
        0 => 'Muito Fraco',
        1 => 'Fraco',
        2 => 'Suficiente',
        3 => 'Forte',
        4 => 'Muito Forte',
    ];

    expect(Password::strengthLabel(3, $labels))->toBe('Forte');
});

test('strengthLabel with partial custom labels uses default for missing', function () {
    $labels = [3 => 'Forte'];

    expect(Password::strengthLabel(3, $labels))->toBe('Forte');
    expect(Password::strengthLabel(1, $labels))->toBe('Weak');
});

test('strengthLabel returns default for unknown score', function () {
    expect(Password::strengthLabel(99))->toBe('Very Weak');
});

// ---------------------------------------------------------------
// validate()
// ---------------------------------------------------------------

test('validate passes with all rules met', function () {
    $rules = ['min' => 8, 'uppercase' => true, 'numbers' => true];

    expect(Password::validate('MyP4ssword', $rules))->toBeTrue();
});

test('validate fails on min length', function () {
    $rules = ['min' => 10];

    expect(Password::validate('short', $rules))->toBeFalse();
});

test('validate fails on missing uppercase', function () {
    $rules = ['uppercase' => true];

    expect(Password::validate('lowercase', $rules))->toBeFalse();
});

test('validate fails on missing lowercase', function () {
    $rules = ['lowercase' => true];

    expect(Password::validate('UPPERCASE', $rules))->toBeFalse();
});

test('validate fails on missing numbers', function () {
    $rules = ['numbers' => true];

    expect(Password::validate('NoNumbers', $rules))->toBeFalse();
});

test('validate fails on missing symbols', function () {
    $rules = ['symbols' => true];

    expect(Password::validate('NoSymbols1', $rules))->toBeFalse();
});

test('validate passes empty rules', function () {
    expect(Password::validate('anything', []))->toBeTrue();
});
