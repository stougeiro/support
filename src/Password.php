<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Password helper.
     *
     * Provides utilities for generating, validating, and inspecting password strings.
     * Focuses on strength checks, normalization, and safe handling of sensitive data.
     * All methods are static and intended for low‑level authentication support.
     */
    final class Password
    {
        /**
         * @param string $password 
         * @param int $cost 
         * @return string 
         */
        public static function hash(string $password, int $cost = 12): string
        {
            return password_hash($password, PASSWORD_BCRYPT, [
                'cost' => $cost,
            ]);
        }

        /**
         * @param string $password 
         * @param string $hash 
         * @return bool 
         */
        public static function verify(string $password, string $hash): bool
        {
            return password_verify($password, $hash);
        }

        /**
         * @param string $hash 
         * @param int $cost 
         * @return bool 
         */
        public static function rehash(string $hash, int $cost = 12): bool
        {
            return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => $cost]);
        }
    }
