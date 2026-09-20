![PHP](https://img.shields.io/badge/PHP-%20^8.2-777BB4)
![PHPStan-Level](https://img.shields.io/badge/PHPStan-Level%209-224488)
![Pest-php](https://img.shields.io/badge/Tests-Passed-019733)
![License](https://img.shields.io/badge/License-MIT-777)

# Support

A lightweight collection of utility helpers designed to provide small, predictable, and strongly typed building blocks for PHP applications. Each helper focuses on a single responsibility, offering safe and consistent operations for arrays, strings, files, directories, tokens, dates, and more.


## ✨ Features

- **Minimalistic helpers**
  Small, focused classes that solve common problems without adding complexity.

- **Strong typing**
  Fully compatible with PHPStan Level 9, ensuring maximum static-analysis integrity.

- **Consistent API**
  Static methods, clear naming, predictable behavior.

- **No dependencies**
  Pure PHP utilities — fast, portable, and framework-agnostic.

- **Safety-oriented**
  Careful handling of strings, paths, files, tokens, and sensitive values.


## 📦 Installation

Install via Composer:

```bash
composer require stougeiro/support
```


## Available Classes

### Arr

Array manipulation: safe wrapping, flattening, dot notation, and polyfills for PHP 8.4/8.5 functions.

| Method | Description |
|---|---|
| `empty()` | Checks whether an array is empty |
| `kshift()` | Removes and returns the first element (queue shift) |
| `kpop()` | Removes and returns the last element (stack pop) |
| `grab()` | Removes and returns a value by key |
| `wrap()` | Wraps a value in an array (null -> empty array) |
| `keysExists()` | Checks whether all given keys exist |
| `only()` | Returns a new array containing only the given keys |
| `except()` | Returns a new array excluding the given keys |
| `flatten()` | Flattens a nested array into a single dimension |
| `any()` | Polyfill for `array_any()` (PHP 8.4) |
| `all()` | Polyfill for `array_all()` (PHP 8.4) |
| `find()` | Polyfill for `array_find()` (PHP 8.4) |
| `findKey()` | Polyfill for `array_find_key()` (PHP 8.4) |
| `first()` | Polyfill for `array_first()` (PHP 8.5) |
| `last()` | Polyfill for `array_last()` (PHP 8.5) |
| `dot()` | Flattens a nested array into dot notation |
| `undot()` | Converts a dot notation array back into a nested array |
| `random()` | Returns N random key => value pairs |

```php
use STDW\Support\Arr;

Arr::dot(['a' => ['b' => ['c' => 1]]]);
// ['a.b.c' => 1]

Arr::undot(['a.b.c' => 1]);
// ['a' => ['b' => ['c' => 1]]]

Arr::only(['a' => 1, 'b' => 2, 'c' => 3], ['a', 'c']);
// ['a' => 1, 'c' => 3]
```


### Base64

URL-safe Base64 encoding and decoding following RFC 4648 section 5.

| Method | Description |
|---|---|
| `encode()` | Encodes a string to URL-safe Base64 |
| `decode()` | Decodes a URL-safe Base64 string |

```php
use STDW\Support\Base64;

$encoded = Base64::encode('hello world');
// 'aGVsbG8gd29ybGQ' (no +, /, or = characters)

Base64::decode($encoded);
// 'hello world'
```


### Date

Date validation, conversion, diff, and relative time strings.

| Method | Description |
|---|---|
| `isValidDate()` | Validates a date string in Y-m-d format |
| `isValidTime()` | Validates a time string in HH:MM or HH:MM:SS format |
| `isValidDateTime()` | Validates a datetime string |
| `convert()` | Converts a date string from one format to another |
| `diff()` | Returns the difference between two dates as int values |
| `withTimezone()` | Executes a callback with a specific timezone, then reverts |
| `ago()` | Returns a human-readable relative time string for past dates |
| `fromNow()` | Returns a human-readable relative time string for future dates |

```php
use STDW\Support\Date;

Date::isValidDate('2025-03-15');
// true

Date::convert('15/03/2025', 'd/m/Y', 'Y-m-d');
// '2025-03-15'

Date::ago('2025-01-01');
// '2 months ago'

Date::fromNow('2025-06-01');
// 'in 3 months'
```


### FileSystem

File and directory operations: size, copy, mkdir, glob, MIME detection.

| Method | Description |
|---|---|
| `absolute()` | Normalizes a path to absolute form without touching the filesystem |
| `size()` | Returns the size of a file or directory in bytes |
| `unit()` | Converts bytes to a human-readable string (KB, MB, GB, TB) |
| `mkdir()` | Creates a directory recursively |
| `rrmdir()` | Recursively removes a directory and all its contents |
| `copy()` | Copies a file or directory to a new location |
| `rglob()` | Recursively globs files matching a pattern |
| `mime()` | Returns the MIME type of a file |

```php
use STDW\Support\FileSystem;

FileSystem::unit(1048576);
// '1.00 MB'

FileSystem::size('/path/to/directory');
// 123456 (bytes)

FileSystem::rglob('/path/to/**/*.php');
// ['/path/to/src/Foo.php', '/path/to/src/Bar.php']
```


### Password

Password hashing, verification, upgrade, strength scoring, and validation.

| Method | Description |
|---|---|
| `hash()` | Hashes a password using bcrypt or argon2id |
| `verify()` | Verifies a password against a hash |
| `needsRehash()` | Checks if a hash needs rehashing |
| `upgrade()` | Upgrades a password hash if needed |
| `algo()` | Detects the algorithm used in a password hash |
| `strength()` | Returns a strength score from 0 (very weak) to 4 (very strong) |
| `strengthLabel()` | Returns a human-readable label for a strength score |
| `validate()` | Validates a password against a set of rules |

```php
use STDW\Support\Password;

$hash = Password::hash('secret');
Password::verify('secret', $hash);
// true

Password::strength('MyP@ssw0rd!');
// 4

Password::validate('weak', ['min' => 8]);
// false
```


### Str

String inspection, transformation, and normalization.

| Method | Description |
|---|---|
| `empty()` | Checks whether a string is empty or whitespace-only |
| `ttrim()` | Trims and collapses internal whitespace into single spaces |
| `onlyNumbers()` | Removes all non-digit characters from a string |
| `mask()` | Applies a mask template to a value string (multibyte-safe) |
| `slugify()` | Converts a string to a URL-friendly slug with transliteration |
| `isFqcn()` | Validates whether a string is a valid fully-qualified class name |

```php
use STDW\Support\Str;

Str::slugify('São Paulo: A Capital!');
// 'sao-paulo-a-capital'

Str::mask('(##) #####-####', '11999991234');
// '(11) 99999-1234'

Str::onlyNumbers('(11) 99999-1234');
// '11999991234'

Str::isFqcn('App\Models\User');
// true
```


### TTL

Time-to-live duration helpers with fixed constants.

| Method | Description |
|---|---|
| `none()` | Returns 0 (immediate expiration) |
| `seconds()` | Converts seconds to an integer TTL |
| `minutes()` | Converts minutes to an integer TTL in seconds |
| `hours()` | Converts hours to an integer TTL in seconds |
| `days()` | Converts days to an integer TTL in seconds |
| `weeks()` | Converts weeks to an integer TTL in seconds |
| `months()` | Converts months to an integer TTL in seconds (30 days/month) |
| `years()` | Converts years to an integer TTL in seconds (365 days/year) |
| `forever()` | Returns PHP_INT_MAX (never expires) |

```php
use STDW\Support\TTL;

TTL::minutes(30);
// 1800

TTL::days(7);
// 604800

TTL::forever();
// PHP_INT_MAX
```


## 🧠 Why?

Modern applications often need small, reliable helpers — but frameworks tend to ship bloated or inconsistent utilities. Support solves this by providing:

- **Predictable behavior** — every method does exactly what its name says, nothing more.
- **Strict typing** — fully compatible with PHPStan Level 9, the highest level of static analysis available. This catches bugs before they reach production.
- **Zero dependencies** — pure PHP, no third-party packages. Fast, portable, and framework-agnostic.
- **Comprehensive test coverage** — 348 tests (unit, feature, and performance) ensure every method works correctly across edge cases, multibyte strings, and real-world scenarios.
- **Safe operations** — careful handling of null inputs, multibyte characters, encoding fallbacks, and filesystem paths.

It's a foundation you can trust, especially when building systems that value clarity, correctness, and long-term maintainability.


## 🤝 Contributing

Contributions are welcome.
Feel free to open issues or submit pull requests.

<br>

[<img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" width="170"/>](https://www.buymeacoffee.com/stougeiro)
