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
         * @param string|null $text 
         * @return bool 
         */
        public static function empty(?string $text): bool
        {
            return trim($text ?? '') === '';
        }

        /**
         * @param string $text 
         * @return string 
         */
        public static function ttrim(string $text): string
        {
            return (string) preg_replace('/\s+/', ' ', trim($text));
        }

        /**
         * @param string|null $text 
         * @return string 
         */
        public static function onlyNumbers(?string $text): string
        {    
            return (string) preg_replace('/\D+/', '', $text ?? '');
        }

        /**
         * @param string $mask 
         * @param string $value 
         * @param string $char 
         * @return string|null 
         */
        public static function mask(string $mask, string $value, string $char = '#'): ?string
        {
            $template = str_replace($char, '%s', $mask);

            try {
                return sprintf($template, ...str_split($value));
            } catch (ArgumentCountError) {
                return null;
            }
        }

        /**
         * @param string $text 
         * @return string 
         */
        public static function slugify(string $text): string
        {
            // Remove acentos e translitera
            $text = (string) iconv('UTF-8', 'ASCII//TRANSLIT', $text);
            // Substitui qualquer grupo de não-letras/dígitos por hífen
            $text = (string) preg_replace('/[^a-zA-Z0-9]+/', '-', $text);
            // Remove hífens duplicados e bordas
            $text = trim($text, '-');
            $text = (string) preg_replace('/-+/', '-', $text);

            return strtolower($text ?: '');
        }

        /**
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
    }
