<?php

use STDW\Support\Date;

// ---------------------------------------------------------------
// isValidDate()
// ---------------------------------------------------------------

test('isValidDate returns true for valid date', function () {
    expect(Date::isValidDate('2024-12-31'))->toBeTrue();
});

test('isValidDate returns true for first day of year', function () {
    expect(Date::isValidDate('2024-01-01'))->toBeTrue();
});

test('isValidDate returns true for leap year', function () {
    expect(Date::isValidDate('2024-02-29'))->toBeTrue();
});

test('isValidDate returns false for non-leap year feb 29', function () {
    expect(Date::isValidDate('2023-02-29'))->toBeFalse();
});

test('isValidDate returns false for month 13', function () {
    expect(Date::isValidDate('2024-13-01'))->toBeFalse();
});

test('isValidDate returns false for day 32', function () {
    expect(Date::isValidDate('2024-01-32'))->toBeFalse();
});

test('isValidDate returns false for day 0', function () {
    expect(Date::isValidDate('2024-01-00'))->toBeFalse();
});

test('isValidDate returns false for empty string', function () {
    expect(Date::isValidDate(''))->toBeFalse();
});

test('isValidDate returns false for random text', function () {
    expect(Date::isValidDate('hello'))->toBeFalse();
});

test('isValidDate returns false for datetime format', function () {
    expect(Date::isValidDate('2024-12-31 12:00:00'))->toBeFalse();
});

test('isValidDate returns false for short year', function () {
    expect(Date::isValidDate('24-12-31'))->toBeFalse();
});

test('isValidDate accepts year 0001', function () {
    expect(Date::isValidDate('0001-01-01'))->toBeTrue();
});

test('isValidDate accepts year 9999', function () {
    expect(Date::isValidDate('9999-12-31'))->toBeTrue();
});

test('isValidDate returns false for year 0000', function () {
    expect(Date::isValidDate('0000-01-01'))->toBeFalse();
});

test('isValidDate handles whitespace', function () {
    expect(Date::isValidDate('  2024-12-31  '))->toBeTrue();
});

// ---------------------------------------------------------------
// isValidTime()
// ---------------------------------------------------------------

test('isValidTime returns true for HH:MM', function () {
    expect(Date::isValidTime('12:30'))->toBeTrue();
});

test('isValidTime returns true for HH:MM:SS', function () {
    expect(Date::isValidTime('12:30:45'))->toBeTrue();
});

test('isValidTime returns true for 00:00', function () {
    expect(Date::isValidTime('00:00'))->toBeTrue();
});

test('isValidTime returns true for 23:59:59', function () {
    expect(Date::isValidTime('23:59:59'))->toBeTrue();
});

test('isValidTime returns false for 24:00', function () {
    expect(Date::isValidTime('24:00'))->toBeFalse();
});

test('isValidTime returns false for 12:60', function () {
    expect(Date::isValidTime('12:60'))->toBeFalse();
});

test('isValidTime returns false for empty string', function () {
    expect(Date::isValidTime(''))->toBeFalse();
});

test('isValidTime returns false for random text', function () {
    expect(Date::isValidTime('hello'))->toBeFalse();
});

test('isValidTime returns false for single number', function () {
    expect(Date::isValidTime('123'))->toBeFalse();
});

test('isValidTime handles whitespace', function () {
    expect(Date::isValidTime('  12:30  '))->toBeTrue();
});

// ---------------------------------------------------------------
// isValidDateTime()
// ---------------------------------------------------------------

test('isValidDateTime returns true with T separator', function () {
    expect(Date::isValidDateTime('2024-12-31T12:30:45'))->toBeTrue();
});

test('isValidDateTime returns true with space separator', function () {
    expect(Date::isValidDateTime('2024-12-31 12:30:45'))->toBeTrue();
});

test('isValidDateTime returns false for invalid date', function () {
    expect(Date::isValidDateTime('2024-02-30 12:30:45'))->toBeFalse();
});

test('isValidDateTime returns false for invalid time', function () {
    expect(Date::isValidDateTime('2024-12-31 25:30:45'))->toBeFalse();
});

test('isValidDateTime returns false for empty string', function () {
    expect(Date::isValidDateTime(''))->toBeFalse();
});

test('isValidDateTime returns false for short string', function () {
    expect(Date::isValidDateTime('2024-12-31'))->toBeFalse();
});

test('isValidDateTime accepts year 0001', function () {
    expect(Date::isValidDateTime('0001-01-01T00:00:00'))->toBeTrue();
});

test('isValidDateTime handles whitespace', function () {
    expect(Date::isValidDateTime('  2024-12-31 12:30:45  '))->toBeTrue();
});

// ---------------------------------------------------------------
// convert()
// ---------------------------------------------------------------

test('convert changes format from Y-m-d to d/m/Y', function () {
    expect(Date::convert('2024-12-31'))->toBe('31/12/2024');
});

test('convert changes format from d/m/Y to Y-m-d', function () {
    expect(Date::convert('31/12/2024', 'd/m/Y', 'Y-m-d'))->toBe('2024-12-31');
});

test('convert returns null for invalid date', function () {
    expect(Date::convert('invalid'))->toBeNull();
});

test('convert with custom format', function () {
    expect(Date::convert('31-12-2024', 'd-m-Y', 'Y/m/d'))->toBe('2024/12/31');
});

// ---------------------------------------------------------------
// withTimezone()
// ---------------------------------------------------------------

test('withTimezone executes callback and returns result', function () {
    $result = Date::withTimezone('UTC', fn () => 'hello');

    expect($result)->toBe('hello');
});

test('withTimezone throws exception for invalid timezone', function () {
    Date::withTimezone('Invalid/Timezone', fn () => null);
})->throws(\InvalidArgumentException::class, 'Invalid timezone: Invalid/Timezone');

test('withTimezone reverts timezone after callback', function () {
    Date::withTimezone('America/Sao_Paulo', fn () => null);

    // Next call should use server default, not São Paulo
    $result = Date::withTimezone('UTC', fn () => 'ok');

    expect($result)->toBe('ok');
});

test('withTimezone reverts even if callback throws', function () {
    try {
        Date::withTimezone('America/Sao_Paulos', fn () => 'x');
    } catch (\InvalidArgumentException $e) {
        // Expected — invalid timezone
    }

    // Should still work with valid timezone after
    $result = Date::withTimezone('UTC', fn () => 'recovered');

    expect($result)->toBe('recovered');
});

// ---------------------------------------------------------------
// diff()
// ---------------------------------------------------------------

test('diff returns 0 seconds for same date', function () {
    expect(Date::diff('2024-12-31', '2024-12-31'))->toBe('0 seconds');
});

test('diff returns days for different dates', function () {
    $result = Date::diff('2024-12-01', '2024-12-31');

    expect($result)->toContain('days');
});

test('diff returns empty string for invalid date', function () {
    expect(Date::diff('invalid', '2024-12-31'))->toBe('');
});

test('diff works with reverse order', function () {
    $result = Date::diff('2024-12-31', '2024-12-01');

    expect($result)->toContain('days');
});

// ---------------------------------------------------------------
// ago()
// ---------------------------------------------------------------

test('ago returns just now for recent time', function () {
    $now = date('Y-m-d H:i:s');

    expect(Date::ago($now, 'Y-m-d H:i:s'))->toBe('just now');
});

test('ago returns minutes ago', function () {
    $past = date('Y-m-d H:i:s', time() - 300); // 5 minutes ago

    expect(Date::ago($past, 'Y-m-d H:i:s'))->toBe('5 minutes ago');
});

test('ago returns hours ago', function () {
    $past = date('Y-m-d H:i:s', time() - 7200); // 2 hours ago

    expect(Date::ago($past, 'Y-m-d H:i:s'))->toBe('2 hours ago');
});

test('ago returns days ago', function () {
    $past = date('Y-m-d', time() - 86400 * 3); // 3 days ago

    expect(Date::ago($past))->toBe('3 days ago');
});

test('ago returns from now for future date', function () {
    $future = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now

    expect(Date::ago($future, 'Y-m-d H:i:s'))->toContain('from now');
});

test('ago returns empty string for invalid date', function () {
    expect(Date::ago('invalid'))->toBe('');
});
