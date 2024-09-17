<?php declare(strict_types=1);

    namespace STDW\Support;


    final class SafeBase64
    {
        public static function encode(string $_string ): string
        {
            return strtr( base64_encode($_string), '+/=', ',-_');
        }

        public static function decode(string $_string ): string
        {
            return base64_decode( strtr($_string, ',-_', '+/='));
        }
    }