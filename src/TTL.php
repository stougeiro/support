<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Time To Live helper.
     *
     * Provides simple utilities for calculating and normalizing TTL values.
     * Focuses on integer‑based expiration handling, ensuring consistent
     * duration computations and safe normalization of negative values.
     * All methods are static and intended for low‑level timing support.
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
         * @return int 
         */
        public static function none(): int
        {
            return self::NONE;
        }

        /**
         * @return int 
         */
        public static function seconds(int $seconds = 1): int
        {
            return max(0, self::SECOND * $seconds);
        }

        /**
         * @return int 
         */
        public static function minutes(int $minutes = 1): int
        {
            return max(0, self::MINUTE * $minutes);
        }

        /**
         * @return int 
         */
        public static function hours(int $hours = 1): int
        {
            return max(0, self::HOUR * $hours);
        }

        /**
         * @return int 
         */
        public static function days(int $days = 1): int
        {
            return max(0, self::DAY * $days);
        }

        /**
         * @return int 
         */
        public static function weeks(int $weeks = 1): int
        {
            return max(0, self::WEEK * $weeks);
        }

        /**
         * @return int 
         */
        public static function months(int $months = 1): int
        {
            return max(0, self::MONTH * $months);
        }

        /**
         * @return int 
         */
        public static function years(int $years = 1): int
        {
            return max(0, self::YEAR * $years);
        }

        /**
         * @return int 
         */
        public static function forever(): int
        {
            return self::FOREVER;
        }
    }
