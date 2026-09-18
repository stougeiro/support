<?php declare(strict_types=1);

    namespace STDW\Support;

    use DateTime;
    use DateTimeZone;
    use InvalidArgumentException;


    /**
     * Date helper.
     *
     * Provides lightweight utilities for validating, normalizing, and comparing
     * date strings. Focuses on strict, format-aware checks to ensure safe
     * date handling. Supports scoped timezone configuration via withTimezone().
     * All methods are static and intended for low-level support operations.
     */
    final class Date
    {
        private static ?string $defaultTimezone = null;


        /**
         * Validates a date string in Y-m-d format.
         *
         * Uses regex for format validation, then checkdate() for semantic
         * validation (e.g., rejects 2024-02-30). Accepts years 0001–9999.
         * Complexity: O(1).
         *
         * @param string $date
         * @return bool
         */
        public static function isValidDate(string $date): bool
        {
            $date = trim($date);

            if ($date === '' || strlen($date) !== 10) {
                return false;
            }

            $regex = '/^([0-9]{4})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/';

            if ( ! preg_match($regex, $date, $matches)) {
                return false;
            }

            return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
        }

        /**
         * Validates a time string in HH:MM or HH:MM:SS format.
         *
         * Uses regex only — no semantic validation needed.
         * Complexity: O(1).
         *
         * @param string $time
         * @return bool
         */
        public static function isValidTime(string $time): bool
        {
            $time = trim($time);

            if ($time === '' || ! in_array(strlen($time), [5, 8], true)) {
                return false;
            }

            return preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9])(:[0-5][0-9])?$/', $time) === 1;
        }

        /**
         * Validates a datetime string in Y-m-d H:i:s or Y-m-dTH:i:s format.
         *
         * Uses regex for format validation, then checkdate() for date semantics.
         * Accepts years 0001–9999. Strict length check (19 chars).
         * Complexity: O(1).
         *
         * @param string $datetime
         * @return bool
         */
        public static function isValidDateTime(string $datetime): bool
        {
            $datetime = trim($datetime);

            if ($datetime === '' || strlen($datetime) !== 19) {
                return false;
            }

            $regex = '/^([0-9]{4})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])(?: |T)([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/';

            if ( ! preg_match($regex, $datetime, $matches)) {
                return false;
            }

            return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
        }

        /**
         * Converts a date string from one format to another.
         *
         * Uses date_create_from_format() for format-aware parsing.
         * Returns null on invalid input. Optionally accepts a timezone.
         * Complexity: O(1).
         *
         * @param string $date
         * @param string $from
         * @param string $to
         * @param string|null $timezone
         * @return string|null
         */
        public static function convert(string $date, string $from = 'Y-m-d', string $to = 'd/m/Y', ?string $timezone = null): ?string
        {
            $d = date_create_from_format($from, $date, self::resolveTimezone($timezone));

            return $d ? date_format($d, $to) : null;
        }

        /**
         * Returns the difference between two dates as int values.
         *
         * Returns result (total seconds) and parts (years, months, days,
         * hours, minutes, seconds). Both values are always non-negative.
         * Returns zeros on invalid input.
         * Complexity: O(1).
         *
         * @param string $date1
         * @param string $date2
         * @param string $format
         * @return false|array{
         *   result: int,
         *   parts: array{
         *     years: int,
         *     months: int,
         *     days: int,
         *     hours: int,
         *     minutes: int,
         *     seconds: int
         * }}
         */
        public static function diff(string $date1, string $date2, string $format = 'Y-m-d'): false|array
        {
            $d1 = date_create_from_format($format, $date1, self::resolveTimezone(null));
            $d2 = date_create_from_format($format, $date2, self::resolveTimezone(null));

            if ($d1 === false || $d2 === false) {
                return false;
            }

            $interval = $d1->diff($d2);

            return [
                'result' => ($interval->days * 86400) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s,
                'parts' => [
                    'years' => $interval->y,
                    'months' => $interval->m,
                    'days' => $interval->d,
                    'hours' => $interval->h,
                    'minutes' => $interval->i,
                    'seconds' => $interval->s,
                ],
            ];
        }

        /**
         * Executes a callback with a specific timezone, then reverts.
         *
         * All Date methods called inside the closure use the given timezone.
         * The previous timezone (or null for server default) is automatically
         * restored after the callback, even if an exception is thrown.
         * Throws InvalidArgumentException for invalid timezone identifiers.
         * Complexity: O(1) for setup, callback-dependent for execution.
         *
         * @template T
         * @param string $timezone
         * @param callable(): T $callback
         * @return T
         * @throws InvalidArgumentException
         */
        public static function withTimezone(string $timezone, callable $callback): mixed
        {
            if ( ! in_array($timezone, DateTimeZone::listIdentifiers(), true)) {
                throw new InvalidArgumentException("Invalid timezone: {$timezone}");
            }

            $previous = self::$defaultTimezone;
            self::$defaultTimezone = $timezone;

            try {
                $result = $callback();
            } finally {
                self::$defaultTimezone = $previous;
            }

            return $result;
        }

        /**
         * Returns a human-readable relative time string for past dates.
         *
         * Shows the largest non-zero unit: "just now", "5 minutes ago", "3 days ago".
         * Returns empty string if the date is in the future or on invalid input.
         * Complexity: O(1).
         *
         * @param string $date
         * @param string $format
         * @param array<string, string> $labels
         * @return string
         */
        public static function ago(string $date, string $format = 'Y-m-d', array $labels = []): string
        {
            $parsed = date_create_from_format($format, $date, self::resolveTimezone(null));

            if ($parsed === false) {
                return '';
            }

            $now = new DateTime('now', self::resolveTimezone(null));

            if ($parsed->getTimestamp() >= $now->getTimestamp()) {
                return '';
            }

            $diff = self::diff($now->format('Y-m-d H:i:s'), $parsed->format('Y-m-d H:i:s'), 'Y-m-d H:i:s');

            if ($diff === false) {
                return '';
            }

            return self::formatRelative($diff['result'], $labels, 'ago');
        }

        /**
         * Returns a human-readable relative time string for future dates.
         *
         * Shows the largest non-zero unit: "in a few seconds", "in 5 minutes", "in 3 days".
         * Returns empty string if the date is in the past or on invalid input.
         * Complexity: O(1).
         *
         * @param string $date
         * @param string $format
         * @param array<string, string> $labels
         * @return string
         */
        public static function fromNow(string $date, string $format = 'Y-m-d', array $labels = []): string
        {
            $parsed = date_create_from_format($format, $date, self::resolveTimezone(null));

            if ($parsed === false) {
                return '';
            }

            $now = new DateTime('now', self::resolveTimezone(null));

            if ($parsed->getTimestamp() <= $now->getTimestamp()) {
                return '';
            }

            $diff = self::diff($parsed->format('Y-m-d H:i:s'), $now->format('Y-m-d H:i:s'), 'Y-m-d H:i:s');

            if ($diff === false) {
                return '';
            }

            return self::formatRelative($diff['result'], $labels, 'from_now');
        }



        /**
         * Resolves the timezone to use: explicit > withTimezone default > server default.
         *
         * @param string|null $timezone
         * @return DateTimeZone
         */
        private static function resolveTimezone(?string $timezone): DateTimeZone
        {
            $tz = $timezone ?? self::$defaultTimezone ?? date_default_timezone_get();

            return new DateTimeZone($tz);
        }

        /**
         * Formats a seconds count into a human-readable relative string.
         *
         * Uses the largest non-zero unit. Returns "just now" for <60s.
         * Complexity: O(1).
         *
         * @param int $seconds
         * @param array<string, string> $labels
         * @param string $suffix
         * @return string
         */
        private static function formatRelative(int $seconds, array $labels, string $suffix): string
        {
            if ($seconds < 60) {
                return $suffix === 'from_now'
                    ? ($labels['just_now'] ?? 'in a few seconds')
                    : ($labels['just_now'] ?? 'just now');
            }

            if ($seconds < 3600) {
                $value = (int) floor($seconds / 60);
                $unit = $value > 1
                    ? ($labels['minutes'] ?? 'minutes')
                    : ($labels['minute'] ?? 'minute');

                return self::formatUnit($value, $unit, $labels, $suffix);
            }

            if ($seconds < 86400) {
                $value = (int) floor($seconds / 3600);
                $unit = $value > 1
                    ? ($labels['hours'] ?? 'hours')
                    : ($labels['hour'] ?? 'hour');

                return self::formatUnit($value, $unit, $labels, $suffix);
            }

            if ($seconds < 2592000) {
                $value = (int) floor($seconds / 86400);
                $unit = $value > 1
                    ? ($labels['days'] ?? 'days')
                    : ($labels['day'] ?? 'day');

                return self::formatUnit($value, $unit, $labels, $suffix);
            }

            if ($seconds < 31536000) {
                $value = (int) floor($seconds / 2592000);
                $unit = $value > 1
                    ? ($labels['months'] ?? 'months')
                    : ($labels['month'] ?? 'month');

                return self::formatUnit($value, $unit, $labels, $suffix);
            }

            $value = (int) floor($seconds / 31536000);
            $unit = $value > 1
                ? ($labels['years'] ?? 'years')
                : ($labels['year'] ?? 'year');

            return self::formatUnit($value, $unit, $labels, $suffix);
        }

        /**
         * @param int $value
         * @param string $unit
         * @param array<string, string> $labels
         * @param string $suffix
         * @return string
         */
        private static function formatUnit(int $value, string $unit, array $labels, string $suffix): string
        {
            $formatted = $value .' '. $unit;

            if ($suffix === 'from_now') {
                return ($labels['from_now'] ?? 'in') .' '. $formatted;
            }

            return $formatted .' '. ($labels['ago'] ?? 'ago');
        }
    }
