<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Safe Base64 helper.
     *
     * Provides URL‑safe Base64 encoding and decoding utilities.
     * Normalizes padding, replaces unsafe characters, and ensures
     * consistent handling of binary data across different environments.
     * All methods are static and intended for low‑level encoding support.
     */
    final class SafeBase64
    {
        /**
         * @param string $text 
         * @return string 
         */
        public static function encode(string $text ): string
        {
            return strtr( base64_encode($text), '+/=', ',-_');
        }

        /**
         * @param string $text 
         * @return string 
         */
        public static function decode(string $text ): string
        {
            $decoded = base64_decode( strtr($text, ',-_', '+/='), true);

            return ($decoded === false) ? '' : $decoded;
        }
    }
