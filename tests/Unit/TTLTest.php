<?php

use STDW\Support\TTL;

// ---------------------------------------------------------------
// none()
// ---------------------------------------------------------------

test('none returns 0', function () {
    expect(TTL::none())->toBe(0);
});

// ---------------------------------------------------------------
// seconds()
// ---------------------------------------------------------------

test('seconds returns 1 by default', function () {
    expect(TTL::seconds())->toBe(1);
});

test('seconds returns multiplied value', function () {
    expect(TTL::seconds(5))->toBe(5);
});

test('seconds clamps negative to 0', function () {
    expect(TTL::seconds(-1))->toBe(0);
});

test('seconds returns 0 for zero', function () {
    expect(TTL::seconds(0))->toBe(0);
});

// ---------------------------------------------------------------
// minutes()
// ---------------------------------------------------------------

test('minutes returns 60 by default', function () {
    expect(TTL::minutes())->toBe(60);
});

test('minutes returns multiplied value', function () {
    expect(TTL::minutes(5))->toBe(300);
});

test('minutes clamps negative to 0', function () {
    expect(TTL::minutes(-1))->toBe(0);
});

// ---------------------------------------------------------------
// hours()
// ---------------------------------------------------------------

test('hours returns 3600 by default', function () {
    expect(TTL::hours())->toBe(3600);
});

test('hours returns multiplied value', function () {
    expect(TTL::hours(2))->toBe(7200);
});

test('hours clamps negative to 0', function () {
    expect(TTL::hours(-1))->toBe(0);
});

// ---------------------------------------------------------------
// days()
// ---------------------------------------------------------------

test('days returns 86400 by default', function () {
    expect(TTL::days())->toBe(86400);
});

test('days returns multiplied value', function () {
    expect(TTL::days(7))->toBe(604800);
});

test('days clamps negative to 0', function () {
    expect(TTL::days(-1))->toBe(0);
});

// ---------------------------------------------------------------
// weeks()
// ---------------------------------------------------------------

test('weeks returns 604800 by default', function () {
    expect(TTL::weeks())->toBe(604800);
});

test('weeks returns multiplied value', function () {
    expect(TTL::weeks(2))->toBe(1209600);
});

test('weeks clamps negative to 0', function () {
    expect(TTL::weeks(-1))->toBe(0);
});

// ---------------------------------------------------------------
// months()
// ---------------------------------------------------------------

test('months returns 2592000 by default', function () {
    expect(TTL::months())->toBe(2592000);
});

test('months uses 30 days', function () {
    expect(TTL::months(2))->toBe(5184000);
});

test('months clamps negative to 0', function () {
    expect(TTL::months(-1))->toBe(0);
});

// ---------------------------------------------------------------
// years()
// ---------------------------------------------------------------

test('years returns 31536000 by default', function () {
    expect(TTL::years())->toBe(31536000);
});

test('years returns multiplied value', function () {
    expect(TTL::years(2))->toBe(63072000);
});

test('years clamps negative to 0', function () {
    expect(TTL::years(-1))->toBe(0);
});

// ---------------------------------------------------------------
// forever()
// ---------------------------------------------------------------

test('forever returns PHP_INT_MAX', function () {
    expect(TTL::forever())->toBe(PHP_INT_MAX);
});
