<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Date helper.
     *
     * Provides lightweight utilities for validating and normalizing date strings.
     * Focuses on strict, format‑aware checks to ensure safe date handling.
     * All methods are static and intended for low‑level support operations.
     */
    final class Date
    {
        /**
         * @param string $date 
         * @return bool 
         */
        public static function isValidDate(string $date): bool
        {
            $date = trim($date);

            if (Str::empty($date) || strlen($date) !== 10) {
                return false;
            }

            $regex = '/^([2][0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/';

            if ( ! preg_match($regex, $date, $matches)) {
                return false;
            }

            return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
        }

        /**
         * @param string $time 
         * @return bool 
         */
        public static function isValidTime(string $time): bool
        {
            $time = trim($time);

            if (Str::empty($time) || ! in_array(strlen($time), [5, 8], true)) {
                return false;
            }

            return preg_match('/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])(:(?:[0-5][0-9]))?$/', $time) === 1;
        }

        /**
         * @param string $datetime 
         * @return bool 
         */
        public static function isValidDateTime(string $datetime): bool
        {
            $datetime = trim($datetime);

            if (Str::empty($datetime) || strlen($datetime) !== 19) {
                return false;
            }

            $regex = '/^([2][0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])(?: |T)(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/';

            if ( ! preg_match($regex, $datetime, $matches)) {
                return false;
            }

            return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
        }

        /**
         * @param string $date 
         * @param string $from 
         * @param string $to 
         * @return null|string 
         */
        public static function convert(string $date, string $from = 'Y-m-d', string $to = 'd/m/Y'): ?string
        {
            $d = date_create_from_format($from, $date);

            return $d ? date_format($d, $to) : null;
        }
    }
