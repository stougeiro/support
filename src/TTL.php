<?php declare(strict_types=1);

    namespace STDW\Support;


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
            return (self::SECOND * $seconds);
        }

        public static function minutes(int $minutes = 1): int
        {
            return (self::MINUTE * $minutes);
        }

        public static function hours(int $hours = 1): int
        {
            return (self::HOUR * $hours);
        }

        public static function days(int $days = 1): int
        {
            return (self::DAY * $days);
        }

        public static function weeks(int $weeks = 1): int
        {
            return (self::WEEK * $weeks);
        }

        public static function months(int $months = 1): int
        {
            return (self::MONTH * $months);
        }

        public static function years(int $years = 1): int
        {
            return (self::YEAR * $years);
        }

        public static function forever(): int
        {
            return self::FOREVER;
        }
    }