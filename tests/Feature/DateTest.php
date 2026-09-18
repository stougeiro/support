<?php

use STDW\Support\Date;

// ---------------------------------------------------------------
// isValidDate() + convert() roundtrip
// ---------------------------------------------------------------

test('isValidDate and convert roundtrip preserves date', function () {
    $date = '2024-12-31';

    expect(Date::isValidDate($date))->toBeTrue();

    $converted = Date::convert($date, 'Y-m-d', 'd/m/Y');

    expect($converted)->toBe('31/12/2024');

    $back = Date::convert($converted, 'd/m/Y', 'Y-m-d');

    expect($back)->toBe($date);
    expect(Date::isValidDate($back))->toBeTrue();
});

// ---------------------------------------------------------------
// diff() and ago() consistency
// ---------------------------------------------------------------

test('diff and ago are consistent for past dates', function () {
    $past = date('Y-m-d', strtotime('-5 days'));

    $diffResult = Date::diff($past, date('Y-m-d'));
    $agoResult = Date::ago($past);

    expect($diffResult['parts']['days'])->toBe(5);
    expect($agoResult)->toBe('5 days ago');
});

// ---------------------------------------------------------------
// withTimezone() and convert() integration
// ---------------------------------------------------------------

test('withTimezone affects convert behavior', function () {
    $result = Date::withTimezone('UTC', function () {
        return Date::convert('2024-12-31', 'Y-m-d', 'd/m/Y');
    });

    expect($result)->toBe('31/12/2024');
});

// ---------------------------------------------------------------
// diff() and fromNow() consistency
// ---------------------------------------------------------------

test('diff and fromNow are consistent for future dates', function () {
    $future = date('Y-m-d', strtotime('+5 days'));

    $diffResult = Date::diff(date('Y-m-d'), $future);
    $fromNowResult = Date::fromNow($future);

    expect($diffResult['parts']['days'])->toBe(5);
    expect($fromNowResult)->toBe('in 5 days');
});

// ---------------------------------------------------------------
// withTimezone() and ago() integration
// ---------------------------------------------------------------

test('withTimezone affects ago behavior', function () {
    $past = date('Y-m-d H:i:s', time() - 7200);

    $result = Date::withTimezone('UTC', function () use ($past) {
        return Date::ago($past, 'Y-m-d H:i:s');
    });

    expect($result)->toContain('hours ago');
});

// ---------------------------------------------------------------
// Token expiration scenario
// ---------------------------------------------------------------

test('token expiration scenario with diff and fromNow', function () {
    $tokenCreatedAt = date('Y-m-d H:i:s', time() - 3600 * 25);
    $tokenExpiresIn = 24 * 3600;

    $diff = Date::diff($tokenCreatedAt, date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
    $isExpired = $diff['result'] > $tokenExpiresIn;

    expect($isExpired)->toBeTrue();

    $newToken = date('Y-m-d H:i:s');
    $expiresAt = date('Y-m-d H:i:s', strtotime($newToken . ' +24 hours'));

    $remaining = Date::diff(date('Y-m-d H:i:s'), $expiresAt, 'Y-m-d H:i:s');
    $timeLeft = Date::fromNow($expiresAt, 'Y-m-d H:i:s');

    expect($remaining['result'])->toBeGreaterThan(0);
    expect($timeLeft)->toContain('in');
});
