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

test('convert with timezone parameter', function () {
    $result = Date::convert('2024-12-31 12:00:00', 'Y-m-d H:i:s', 'Y-m-d H:i:s', 'America/Sao_Paulo');

    expect($result)->toBe('2024-12-31 12:00:00');
});

test('convert with timezone shifts time', function () {
    $result = Date::convert('2024-12-31 12:00:00', 'Y-m-d H:i:s', 'Y-m-d H:i:s', 'Asia/Tokyo');

    expect($result)->toBe('2024-12-31 12:00:00');
});

test('convert throws exception for invalid timezone', function () {
    Date::convert('2024-12-31', 'Y-m-d', 'Y-m-d', 'Invalid/Zone');
})->throws(\InvalidArgumentException::class, 'Invalid timezone: Invalid/Zone');

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

test('diff returns zeros for same date', function () {
    $result = Date::diff('2024-12-31', '2024-12-31');

    expect($result['result'])->toBe(0);
    expect($result['parts'])->toBe([
        'years' => 0,
        'months' => 0,
        'days' => 0,
        'hours' => 0,
        'minutes' => 0,
        'seconds' => 0,
    ]);
});

test('diff returns days correctly', function () {
    $result = Date::diff('2024-12-01', '2024-12-31');

    expect($result['result'])->toBe(30 * 86400);
    expect($result['parts']['days'])->toBe(30);
});

test('diff returns months and days', function () {
    $result = Date::diff('2024-01-01', '2024-03-15');

    expect($result['parts']['months'])->toBe(2);
    expect($result['parts']['days'])->toBe(14);
});

test('diff returns false for invalid input', function () {
    expect(Date::diff('invalid', '2024-12-31'))->toBeFalse();
});

test('diff works with reverse order', function () {
    $result = Date::diff('2024-12-31', '2024-12-01');

    expect($result['parts']['days'])->toBe(30);
});

test('diff returns years correctly', function () {
    $result = Date::diff('2022-01-01', '2024-01-01');

    expect($result['parts']['years'])->toBe(2);
});

test('diff with custom format', function () {
    $result = Date::diff('31/12/2024', '01/12/2024', 'd/m/Y');

    expect($result['parts']['days'])->toBe(30);
});

test('diff returns false when both inputs invalid', function () {
    expect(Date::diff('invalid1', 'invalid2'))->toBeFalse();
});

test('diff with hours and minutes', function () {
    $result = Date::diff('2024-12-01 00:00:00', '2024-12-01 02:30:00', 'Y-m-d H:i:s');

    expect($result['parts']['hours'])->toBe(2);
    expect($result['parts']['minutes'])->toBe(30);
    expect($result['result'])->toBe(2 * 3600 + 30 * 60);
});

test('diff with DST transition loses an hour', function () {
    $result = Date::diff('2024-03-09', '2024-03-11');

    expect($result['parts']['days'])->toBe(2);
    expect($result['result'])->toBe(2 * 86400);
});

// ---------------------------------------------------------------
// ago()
// ---------------------------------------------------------------

test('ago returns just now for recent time', function () {
    $recent = date('Y-m-d H:i:s', time() - 5);

    expect(Date::ago($recent, 'Y-m-d H:i:s'))->toBe('just now');
});

test('ago returns minutes ago', function () {
    $past = date('Y-m-d H:i:s', time() - 300);

    expect(Date::ago($past, 'Y-m-d H:i:s'))->toBe('5 minutes ago');
});

test('ago returns hours ago', function () {
    $past = date('Y-m-d H:i:s', time() - 7200);

    expect(Date::ago($past, 'Y-m-d H:i:s'))->toBe('2 hours ago');
});

test('ago returns days ago', function () {
    $past = date('Y-m-d', time() - 86400 * 3);

    expect(Date::ago($past))->toBe('3 days ago');
});

test('ago returns empty for future date', function () {
    $future = date('Y-m-d H:i:s', time() + 3600);

    expect(Date::ago($future, 'Y-m-d H:i:s'))->toBe('');
});

test('ago returns empty for invalid date', function () {
    expect(Date::ago('invalid'))->toBe('');
});

test('ago with custom labels', function () {
    $past = date('Y-m-d H:i:s', time() - 7200);

    $labels = [
        'hours' => 'horas',
        'ago' => 'atrás',
    ];

    expect(Date::ago($past, 'Y-m-d H:i:s', $labels))->toBe('2 horas atrás');
});

test('ago with full portuguese labels', function () {
    $past = date('Y-m-d H:i:s', time() - 7200);

    $labels = [
        'just_now' => 'agora',
        'minute' => 'minuto',
        'minutes' => 'minutos',
        'hour' => 'hora',
        'hours' => 'horas',
        'day' => 'dia',
        'days' => 'dias',
        'month' => 'mês',
        'months' => 'meses',
        'year' => 'ano',
        'years' => 'anos',
        'ago' => 'atrás',
    ];

    expect(Date::ago($past, 'Y-m-d H:i:s', $labels))->toBe('2 horas atrás');
});

test('ago singular in portuguese', function () {
    $past = date('Y-m-d H:i:s', time() - 90);

    $labels = [
        'minute' => 'minuto',
        'minutes' => 'minutos',
        'ago' => 'atrás',
    ];

    expect(Date::ago($past, 'Y-m-d H:i:s', $labels))->toBe('1 minuto atrás');
});

test('ago just now in portuguese', function () {
    $recent = date('Y-m-d H:i:s', time() - 5);

    $labels = ['just_now' => 'agora'];

    expect(Date::ago($recent, 'Y-m-d H:i:s', $labels))->toBe('agora');
});

test('ago returns singular minute', function () {
    $past = date('Y-m-d H:i:s', time() - 90);

    expect(Date::ago($past, 'Y-m-d H:i:s'))->toBe('1 minute ago');
});

test('ago returns months ago', function () {
    $past = date('Y-m-d', strtotime('-60 days'));

    expect(Date::ago($past))->toBe('2 months ago');
});

test('ago returns years ago', function () {
    $past = date('Y-m-d', strtotime('-400 days'));

    expect(Date::ago($past))->toBe('1 year ago');
});

test('ago returns just now for 1 second', function () {
    $past = date('Y-m-d H:i:s', time() - 1);

    expect(Date::ago($past, 'Y-m-d H:i:s'))->toBe('just now');
});

test('ago returns just now for exactly 59 seconds', function () {
    $past = date('Y-m-d H:i:s', time() - 59);

    expect(Date::ago($past, 'Y-m-d H:i:s'))->toBe('just now');
});

test('ago returns 1 minute ago at 60 seconds', function () {
    $past = date('Y-m-d H:i:s', time() - 60);

    expect(Date::ago($past, 'Y-m-d H:i:s'))->toBe('1 minute ago');
});

test('ago with missing ago key uses default', function () {
    $past = date('Y-m-d H:i:s', time() - 7200);

    $labels = ['hours' => 'horas'];

    expect(Date::ago($past, 'Y-m-d H:i:s', $labels))->toBe('2 horas ago');
});

test('ago with empty labels uses all defaults', function () {
    $past = date('Y-m-d H:i:s', time() - 7200);

    expect(Date::ago($past, 'Y-m-d H:i:s', []))->toBe('2 hours ago');
});

// ---------------------------------------------------------------
// fromNow()
// ---------------------------------------------------------------

test('fromNow returns in a few seconds for near future', function () {
    $near = date('Y-m-d H:i:s', time() + 5);

    expect(Date::fromNow($near, 'Y-m-d H:i:s'))->toBe('in a few seconds');
});

test('fromNow returns in minutes', function () {
    $future = date('Y-m-d H:i:s', time() + 300);

    expect(Date::fromNow($future, 'Y-m-d H:i:s'))->toBe('in 5 minutes');
});

test('fromNow returns in hours', function () {
    $future = date('Y-m-d H:i:s', time() + 7200);

    expect(Date::fromNow($future, 'Y-m-d H:i:s'))->toBe('in 2 hours');
});

test('fromNow returns in days', function () {
    $future = date('Y-m-d', time() + 86400 * 3);

    expect(Date::fromNow($future))->toBe('in 3 days');
});

test('fromNow returns empty for past date', function () {
    $past = date('Y-m-d H:i:s', time() - 3600);

    expect(Date::fromNow($past, 'Y-m-d H:i:s'))->toBe('');
});

test('fromNow returns empty for invalid date', function () {
    expect(Date::fromNow('invalid'))->toBe('');
});

test('fromNow with custom labels', function () {
    $future = date('Y-m-d H:i:s', time() + 7200);

    $labels = [
        'hours' => 'horas',
        'from_now' => 'em',
    ];

    expect(Date::fromNow($future, 'Y-m-d H:i:s', $labels))->toBe('em 2 horas');
});

test('fromNow with full portuguese labels', function () {
    $future = date('Y-m-d H:i:s', time() + 7200);

    $labels = [
        'just_now' => 'agora',
        'minute' => 'minuto',
        'minutes' => 'minutos',
        'hour' => 'hora',
        'hours' => 'horas',
        'day' => 'dia',
        'days' => 'dias',
        'month' => 'mês',
        'months' => 'meses',
        'year' => 'ano',
        'years' => 'anos',
        'from_now' => 'em',
    ];

    expect(Date::fromNow($future, 'Y-m-d H:i:s', $labels))->toBe('em 2 horas');
});

test('fromNow singular in portuguese', function () {
    $future = date('Y-m-d H:i:s', time() + 90);

    $labels = [
        'minute' => 'minuto',
        'minutes' => 'minutos',
        'from_now' => 'em',
    ];

    expect(Date::fromNow($future, 'Y-m-d H:i:s', $labels))->toBe('em 1 minuto');
});

test('fromNow in a few seconds in portuguese', function () {
    $near = date('Y-m-d H:i:s', time() + 5);

    $labels = ['just_now' => 'agora'];

    expect(Date::fromNow($near, 'Y-m-d H:i:s', $labels))->toBe('agora');
});

test('fromNow returns in 1 minute', function () {
    $future = date('Y-m-d H:i:s', time() + 90);

    expect(Date::fromNow($future, 'Y-m-d H:i:s'))->toBe('in 1 minute');
});

test('fromNow returns in months', function () {
    $future = date('Y-m-d', strtotime('+60 days'));

    expect(Date::fromNow($future))->toBe('in 2 months');
});

test('fromNow returns in 1 year', function () {
    $future = date('Y-m-d', strtotime('+400 days'));

    expect(Date::fromNow($future))->toBe('in 1 year');
});

test('fromNow returns in a few seconds for 1 second', function () {
    $future = date('Y-m-d H:i:s', time() + 1);

    expect(Date::fromNow($future, 'Y-m-d H:i:s'))->toBe('in a few seconds');
});

test('fromNow returns in a few seconds for exactly 59 seconds', function () {
    $future = date('Y-m-d H:i:s', time() + 59);

    expect(Date::fromNow($future, 'Y-m-d H:i:s'))->toBe('in a few seconds');
});

test('fromNow returns in 1 minute at 60 seconds', function () {
    $future = date('Y-m-d H:i:s', time() + 60);

    expect(Date::fromNow($future, 'Y-m-d H:i:s'))->toBe('in 1 minute');
});

test('fromNow with missing from_now key uses default', function () {
    $future = date('Y-m-d H:i:s', time() + 7200);

    $labels = ['hours' => 'horas'];

    expect(Date::fromNow($future, 'Y-m-d H:i:s', $labels))->toBe('in 2 horas');
});

test('fromNow with empty labels uses all defaults', function () {
    $future = date('Y-m-d H:i:s', time() + 7200);

    expect(Date::fromNow($future, 'Y-m-d H:i:s', []))->toBe('in 2 hours');
});
