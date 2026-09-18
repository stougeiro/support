<?php

use STDW\Support\Password;

/**
 * Password performance benchmarks.
 *
 * Run:
 *   vendor/bin/phpbench run tests/Performance/PasswordBenchmark.php --report=default
 */

// ---------------------------------------------------------------
// hash()
// ---------------------------------------------------------------

function bench_hash_bcrypt(): void
{
    for ($i = 0; $i < 10; $i++) {
        Password::hash('benchmark_password_' . $i, PASSWORD_BCRYPT, 4);
    }
}

function bench_hash_argon2id(): void
{
    if ( ! defined('PASSWORD_ARGON2ID')) {
        return;
    }

    for ($i = 0; $i < 10; $i++) {
        Password::hash('benchmark_password_' . $i, PASSWORD_ARGON2ID);
    }
}

// ---------------------------------------------------------------
// verify()
// ---------------------------------------------------------------

function bench_verify(): void
{
    $hash = Password::hash('benchmark_password', PASSWORD_BCRYPT, 4);

    for ($i = 0; $i < 100; $i++) {
        Password::verify('benchmark_password', $hash);
    }
}

// ---------------------------------------------------------------
// upgrade()
// ---------------------------------------------------------------

function bench_upgrade(): void
{
    for ($i = 0; $i < 10; $i++) {
        $hash = Password::hash('benchmark_password', PASSWORD_BCRYPT, 4);
        Password::upgrade('benchmark_password', $hash, PASSWORD_BCRYPT, 12);
    }
}

// ---------------------------------------------------------------
// strength()
// ---------------------------------------------------------------

function bench_strength(): void
{
    $passwords = [
        'short',
        'abcdefgh',
        'Abcdefgh',
        'Abcdef1!',
        'Aa1!Aa1!Aa1!Aa1!',
    ];

    for ($i = 0; $i < 1000; $i++) {
        foreach ($passwords as $password) {
            Password::strength($password);
        }
    }
}

// ---------------------------------------------------------------
// validate()
// ---------------------------------------------------------------

function bench_validate(): void
{
    $rules = ['min' => 8, 'uppercase' => true, 'numbers' => true, 'symbols' => true];

    for ($i = 0; $i < 1000; $i++) {
        Password::validate('MyP4ss!', $rules);
    }
}
