<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Time To Live helper.
     *
     * Provides simple integer-based TTL calculations.
     * Returns a TTL value in seconds.
     * Negative values are normalized to zero.
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


        public static function none(): int
        {
            return self::NONE;
        }

        public static function seconds(int $seconds = 1): int
        {
            return max(0, self::SECOND * $seconds);
        }

        public static function minutes(int $minutes = 1): int
        {
            return max(0, self::MINUTE * $minutes);
        }

        public static function hours(int $hours = 1): int
        {
            return max(0, self::HOUR * $hours);
        }

        public static function days(int $days = 1): int
        {
            return max(0, self::DAY * $days);
        }

        public static function weeks(int $weeks = 1): int
        {
            return max(0, self::WEEK * $weeks);
        }

        public static function months(int $months = 1): int
        {
            return max(0, self::MONTH * $months);
        }

        public static function years(int $years = 1): int
        {
            return max(0, self::YEAR * $years);
        }

        public static function forever(): int
        {
            return self::FOREVER;
        }
    }
