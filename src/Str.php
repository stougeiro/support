<?php declare(strict_types=1);

    namespace STDW\Support;

    use ArgumentCountError;


    /**
     * String helper.
     *
     * Provides lightweight utilities for inspecting, transforming, and normalizing
     * string values. Focuses on safe manipulation, encoding‑aware operations, and
     * consistent behavior across different environments. All methods are static and
     * intended for low‑level string handling support.
     */
    final class Str
    {
        /**
         * Checks whether the given string is empty or whitespace-only.
         *
         * Treats null as empty. Trims the string before comparison,
         * so strings containing only spaces, tabs, or newlines are empty.
         * Complexity: O(n) where n = string length.
         *
         * @param string|null $text
         * @return bool
         */
        public static function empty(?string $text): bool
        {
            return trim($text ?? '') === '';
        }

        /**
         * Trims leading/trailing whitespace and collapses internal whitespace.
         *
         * Replaces any sequence of one or more whitespace characters (spaces,
         * tabs, newlines) with a single space. Result is always trimmed.
         * Complexity: O(n) where n = string length.
         *
         * @param string $text
         * @return string
         */
        public static function ttrim(string $text): string
        {
            return (string) preg_replace('/\s+/', ' ', trim($text));
        }

        /**
         * Removes all non-digit characters from a string.
         *
         * Returns empty string for null input. Useful for extracting
         * numeric values from formatted inputs like phone numbers.
         * Complexity: O(n) where n = string length.
         *
         * @param string|null $text
         * @return string
         */
        public static function onlyNumbers(?string $text): string
        {
            return (string) preg_replace('/\D+/', '', $text ?? '');
        }

        /**
         * Applies a mask template to a value string.
         *
         * Replaces the mask character (default '#') with '%s' and uses sprintf()
         * to fill in each character of the value. Uses preg_split with u flag
         * for multibyte-safe character splitting (handles emojis and accents).
         * Returns null if the value has fewer characters than the mask requires.
         * Complexity: O(n) where n = value length.
         *
         * @param string $mask   Template string, e.g. '###.###.###-##'
         * @param string $value  The value to fill in
         * @param string $char   Placeholder character (default '#')
         * @return string|null   The formatted string or null on mismatch
         */
        public static function mask(string $mask, string $value, string $char = '#'): ?string
        {
            $template = str_replace($char, '%s', $mask);
            $chars = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);

            if ($chars === false) {
                $chars = [];
            }

            try {
                return sprintf($template, ...$chars);
            } catch (ArgumentCountError) {
                return null;
            }
        }

        /**
         * Validates whether a string is a valid fully-qualified class name (FQCN).
         *
         * Each namespace segment must start with an uppercase letter followed by
         * alphanumeric or underscore characters. Leading backslash is tolerated.
         * Returns false for empty segments, empty input, or invalid characters.
         * Complexity: O(n) where n = number of parts.
         *
         * @param string $classname
         * @return bool
         */
        public static function isFqcn(string $classname): bool
        {
            if (self::empty($classname)) {
                return false;
            }

            $classname = ltrim($classname, '\\');
            $parts = explode('\\', $classname);

            foreach ($parts as $part) {
                if (self::empty($part)) {
                    return false;
                }

                if ( ! preg_match('/^[A-Z][a-zA-Z0-9_]*$/', $part)) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Converts a string to a URL-friendly slug.
         *
         * Transliterates UTF-8 accented characters to ASCII equivalents using
         * a two-tier fallback (iconv, then manual map). Replaces non-alphanumeric
         * characters with hyphens, removes duplicates, and trims edges.
         * Complexity: O(n) where n = string length.
         *
         * @param string $text
         * @return string
         */
        public static function slugify(string $text): string
        {
            $text = self::transliterate($text);
            $text = (string) preg_replace('/[^a-zA-Z0-9]+/', '-', $text);
            $text = trim($text, '-');
            $text = (string) preg_replace('/-+/', '-', $text);

            return strtolower($text ?: '');
        }



        /**
         * Latin transliteration map for slug generation.
         *
         * Covers PT-BR, EN, ES, FR, DE, IT accented characters.
         * Used as fallback when iconv is unavailable or returns errors.
         */
        private const TRANSLITERATION_MAP = [
            'À'=>'A','Á'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A','Å'=>'A',
            'Æ'=>'AE','Ç'=>'C','È'=>'E','É'=>'E','Ê'=>'E','Ë'=>'E',
            'Ì'=>'I','Í'=>'I','Î'=>'I','Ï'=>'I','Ð'=>'D','Ñ'=>'N',
            'Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O','Ø'=>'O',
            'Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','Þ'=>'TH',
            'ß'=>'ss',
            'à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a',
            'æ'=>'ae','ç'=>'c','è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
            'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i','ð'=>'d','ñ'=>'n',
            'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'o',
            'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u','ý'=>'y','þ'=>'th',
            'ÿ'=>'y',
        ];

        /**
         * Transliterates UTF-8 characters to ASCII equivalents.
         *
         * Uses a two-tier fallback: iconv (best quality) then a manual latin map.
         * Returns input unchanged on complete failure.
         * Complexity: O(n) where n = string length.
         *
         * @param string $text
         * @return string
         */
        private static function transliterate(string $text): string
        {
            if (function_exists('iconv')) {
                $result = iconv('UTF-8', 'ASCII//TRANSLIT', $text);

                if (is_string($result) && ! str_contains($result, '?')) {
                    return $result;
                }
            }

            return strtr($text, self::TRANSLITERATION_MAP);
        }
    }
