<?php declare(strict_types=1);

    namespace STDW\Support;

    use DateTime;
    use DateInterval;
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
         * Returns a human-readable difference between two dates.
         *
         * Calculates the interval and builds a string like "2 days, 3 hours".
         * Uses resolved timezone (withTimezone > server default).
         * Returns empty string on invalid input. Complexity: O(1).
         *
         * @param string $date1
         * @param string $date2
         * @param string $format
         * @return string
         */
        public static function diff(string $date1, string $date2, string $format = 'Y-m-d'): string
        {
            $d1 = date_create_from_format($format, $date1, self::resolveTimezone(null));
            $d2 = date_create_from_format($format, $date2, self::resolveTimezone(null));

            if ($d1 === false || $d2 === false) {
                return '';
            }

            $interval = $d1->diff($d2);

            return self::formatInterval($interval);
        }

        /**
         * Returns a human-readable relative time string.
         *
         * For past dates: "2 hours ago", "3 days ago", "just now".
         * For future dates: "2 hours from now", "3 days from now".
         * Uses resolved timezone (withTimezone > server default).
         * Returns empty string on invalid input. Complexity: O(1).
         *
         * @param string $date
         * @param string $format
         * @return string
         */
        public static function ago(string $date, string $format = 'Y-m-d'): string
        {
            $parsed = date_create_from_format($format, $date, self::resolveTimezone(null));

            if ($parsed === false) {
                return '';
            }

            $now = new DateTime('now', self::resolveTimezone(null));
            $interval = $now->diff($parsed);

            if ($interval->invert === 0) {
                // Future date
                $parts = self::intervalParts($interval);

                return ($parts === [] ? '0 seconds' : implode(', ', $parts)) . ' from now';
            }

            // Past date
            $diff = time() - $parsed->getTimestamp();

            if ($diff < 60) {
                return 'just now';
            }

            if ($diff < 3600) {
                $minutes = (int) floor($diff / 60);

                return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
            }

            if ($diff < 86400) {
                $hours = (int) floor($diff / 3600);

                return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
            }

            if ($diff < 2592000) {
                $days = (int) floor($diff / 86400);

                return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
            }

            if ($diff < 31536000) {
                $months = (int) floor($diff / 2592000);

                return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
            }

            $years = (int) floor($diff / 31536000);

            return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
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
         * Formats a DateInterval into a human-readable string.
         *
         * @param DateInterval $interval
         * @return string
         */
        private static function formatInterval(DateInterval $interval): string
        {
            $parts = self::intervalParts($interval);

            return $parts === [] ? '0 seconds' : implode(', ', $parts);
        }

        /**
         * Extracts non-zero parts from a DateInterval.
         *
         * @param DateInterval $interval
         * @return list<string>
         */
        private static function intervalParts(\DateInterval $interval): array
        {
            $parts = [];

            if ($interval->y > 0) {
                $parts[] = $interval->y . ' year' . ($interval->y > 1 ? 's' : '');
            }

            if ($interval->m > 0) {
                $parts[] = $interval->m . ' month' . ($interval->m > 1 ? 's' : '');
            }

            if ($interval->d > 0) {
                $parts[] = $interval->d . ' day' . ($interval->d > 1 ? 's' : '');
            }

            if ($interval->h > 0) {
                $parts[] = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '');
            }

            if ($interval->i > 0) {
                $parts[] = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
            }

            if ($interval->s > 0) {
                $parts[] = $interval->s . ' second' . ($interval->s > 1 ? 's' : '');
            }

            return $parts;
        }
    }
