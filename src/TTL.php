<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Time To Live helper.
     *
     * Provides simple utilities for calculating and normalizing TTL values.
     * Focuses on integer-based expiration handling, ensuring consistent
     * duration computations and safe normalization of negative values.
     * All methods are static and intended for low-level timing support.
     *
     * Note: months are normalized to 30 days and years to 365 days.
     * This is intentional — TTL does not require calendar precision.
     */
    final class TTL
    {
        private const NONE = 0;
        private const SECOND = 1;
        private const MINUTE = self::SECOND * 60;
        private const HOUR = self::MINUTE * 60;
        private const DAY = self::HOUR * 24;
        private const WEEK = self::DAY * 7;
        private const MONTH = self::DAY * 30;
        private const YEAR = self::DAY * 365;
        private const FOREVER = PHP_INT_MAX;


        /**
         * Returns 0 (no TTL / immediate expiration).
         *
         * @return int 0
         */
        public static function none(): int
        {
            return self::NONE;
        }

        /**
         * Converts seconds to an integer TTL.
         *
         * @param int $seconds Number of seconds (default: 1)
         * @return int Seconds as TTL, clamped to 0 for negative values
         */
        public static function seconds(int $seconds = 1): int
        {
            return max(0, self::SECOND * $seconds);
        }

        /**
         * Converts minutes to an integer TTL in seconds.
         *
         * @param int $minutes Number of minutes (default: 1)
         * @return int Minutes converted to seconds, clamped to 0 for negative values
         */
        public static function minutes(int $minutes = 1): int
        {
            return max(0, self::MINUTE * $minutes);
        }

        /**
         * Converts hours to an integer TTL in seconds.
         *
         * @param int $hours Number of hours (default: 1)
         * @return int Hours converted to seconds, clamped to 0 for negative values
         */
        public static function hours(int $hours = 1): int
        {
            return max(0, self::HOUR * $hours);
        }

        /**
         * Converts days to an integer TTL in seconds.
         *
         * @param int $days Number of days (default: 1)
         * @return int Days converted to seconds, clamped to 0 for negative values
         */
        public static function days(int $days = 1): int
        {
            return max(0, self::DAY * $days);
        }

        /**
         * Converts weeks to an integer TTL in seconds.
         *
         * @param int $weeks Number of weeks (default: 1)
         * @return int Weeks converted to seconds, clamped to 0 for negative values
         */
        public static function weeks(int $weeks = 1): int
        {
            return max(0, self::WEEK * $weeks);
        }

        /**
         * Converts months to an integer TTL in seconds.
         *
         * Uses 30 days per month (fixed, not calendar-based).
         *
         * @param int $months Number of months (default: 1)
         * @return int Months converted to seconds, clamped to 0 for negative values
         */
        public static function months(int $months = 1): int
        {
            return max(0, self::MONTH * $months);
        }

        /**
         * Converts years to an integer TTL in seconds.
         *
         * Uses 365 days per year (fixed, not calendar-based).
         *
         * @param int $years Number of years (default: 1)
         * @return int Years converted to seconds, clamped to 0 for negative values
         */
        public static function years(int $years = 1): int
        {
            return max(0, self::YEAR * $years);
        }

        /**
         * Returns PHP_INT_MAX (effectively never expires).
         *
         * @return int PHP_INT_MAX
         */
        public static function forever(): int
        {
            return self::FOREVER;
        }
    }
