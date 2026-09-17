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

    expect($diffResult)->toContain('days');
    expect($agoResult)->toContain('days ago');
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
