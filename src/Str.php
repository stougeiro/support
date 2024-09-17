<?php declare(strict_types=1);

    namespace STDW\Support;


    final class Str
    {
        public static function ttrim(string $_string): string
        {
            return preg_replace([
                '/\s{2,}/',
                '/[\t\n]/',
            ], ' ', trim($_string));
        }

        public static function onlyNumbers(string|null $_string): string
        {    
            return preg_replace('/[^0-9]/', '', $_string);
        }

        public static function empty(string|null $_var): bool
        {
            return is_null($_var) || empty($_var) || (preg_replace('/(\s)/i', '', $_var) === '');
        }
    }