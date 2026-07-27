<?php declare(strict_types=1);

    namespace STDW\Support;

    use ArgumentCountError;


    final class Str
    {
        public static function empty(?string $text): bool
        {
            return trim($text ?? '') === '';
        }

        public static function ttrim(string $text): string
        {
            return preg_replace('/\s+/', ' ', trim($text));
        }

        public static function onlyNumbers(?string $text): string
        {    
            return preg_replace('/\D+/', '', $text ?? '');
        }

        public static function mask(string $mask, string $value, string $char = '#'): ?string
        {
            $template = str_replace($char, '%s', $mask);

            try {
                return sprintf($template, ...str_split($value));
            } catch (ArgumentCountError) {
                return null;
            }
        }

        public static function slugify(string $text): string
        {
            // Remove acentos e translitera
            $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
            // Substitui qualquer grupo de não-letras/dígitos por hífen
            $text = preg_replace('/[^a-zA-Z0-9]+/', '-', $text);
            // Remove hífens duplicados e bordas
            $text = trim($text, '-');
            $text = preg_replace('/-+/', '-', $text);

            return strtolower($text ?: '');
        }

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
