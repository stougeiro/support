<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * URL-safe Base64 helper (RFC 4648).
     *
     * Provides URL-safe Base64 encoding and decoding following RFC 4648 §5.
     * Replaces '+' with '-', '/' with '_', and removes padding '='.
     * Decoding re-adds padding automatically and returns false on failure.
     * All methods are static and intended for low-level encoding support.
     */
    final class Base64
    {
        /**
         * Encodes a string to URL-safe Base64 (RFC 4648 §5).
         *
         * Replaces '+' with '-', '/' with '_', and strips trailing '=' padding.
         * Complexity: O(n).
         *
         * @param string $text
         * @return string
         */
        public static function encode(string $text): string
        {
            return rtrim(strtr(base64_encode($text), '+/', '-_'), '=');
        }

        /**
         * Decodes a URL-safe Base64 string (RFC 4648 §5).
         *
         * Restores standard Base64 characters and re-adds padding before
         * decoding. Returns false if the input contains invalid characters.
         * Complexity: O(n).
         *
         * @param string $text
         * @return false|string
         */
        public static function decode(string $text): false|string
        {
            $text = strtr($text, '-_', '+/');
            $mod = strlen($text) % 4;

            if ($mod !== 0) {
                $text .= str_repeat('=', 4 - $mod);
            }

            return base64_decode($text, true);
        }
    }
