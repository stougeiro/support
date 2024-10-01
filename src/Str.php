<?php declare(strict_types=1);

    namespace STDW\Support;


    final class Str
    {
        public static function ttrim(string $text): string
        {
            return preg_replace([
                '/\s{2,}/',
                '/[\t\n]/',
            ], ' ', trim($text));
        }

        public static function onlyNumbers(string|null $text): string
        {    
            return preg_replace('/[^0-9]/', '', $text);
        }

        public static function empty(string|null $text): bool
        {
            return is_null($text) || empty($text) || (preg_replace('/(\s)/i', '', $text) === '');
        }
    }