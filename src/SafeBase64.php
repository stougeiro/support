<?php declare(strict_types=1);

    namespace STDW\Support;


    final class SafeBase64
    {
        public static function encode(string $text ): string
        {
            return strtr( base64_encode($text), '+/=', ',-_');
        }

        public static function decode(string $text ): string
        {
            return base64_decode( strtr($text, ',-_', '+/='));
        }
    }