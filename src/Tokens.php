<?php declare(strict_types=1);

    namespace STDW\Support;

    use RuntimeException;
    use InvalidArgumentException;


    /**
     * Token generation helper.
     *
     * Provides lightweight utilities for producing unique, non‑colliding identifiers.
     * Includes ultra‑light string tokens and Snowflake‑style numeric IDs with optional
     * node differentiation. All methods are static and intended for low‑level identity
     * and correlation support.
     */
    final class Tokens
    {
        /**
         * Generates a ULIF (Universally Unique Lexicographically Sortable Identifier).
         *
         * @return string The generated ULIF.
         * @throws RuntimeException If the PHP version is not 64-bit.
         */
        public static function ulif(): string
        {
            if (PHP_INT_SIZE < 8) {
                throw new RuntimeException("Tokens::ulif: PHP 64-bit is required");
            }

            $ts = (int) floor(microtime(true) * 1000) & 0xFFFFFFFFFFFF;
            $rand = random_bytes(10);
            $tsHigh = ($ts >> 16) & 0xFFFFFFFF;
            $tsLow = $ts & 0xFFFF;
            $binary = pack('N', $tsHigh) . pack('n', $tsLow) . $rand;
            $alphabet = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
            $bits = '';

            for ($i = 0, $len = 16; $i < $len; $i++) {
                $bits .= str_pad(decbin(ord($binary[$i])), 8, '0', STR_PAD_LEFT);
            }

            $bits = str_pad($bits, 130, '0', STR_PAD_RIGHT);
            $out = '';

            for ($i = 0; $i < 26; $i++) {
                $index = (int) bindec(substr($bits, $i * 5, 5));
                $out .= $alphabet[$index];
            }

            return $out;
        }

        /**
         * Generates a Snowflake ID.
         *
         * @param int $node The node ID (0-1023).
         * @return int The generated Snowflake ID.
         * @throws RuntimeException If the PHP version is not 64-bit.
         * @throws InvalidArgumentException If the node ID is out of range.
         */
        public static function snowfake(int $node = 0): int
        {
            if (PHP_INT_SIZE < 8) {
                throw new RuntimeException("Tokens::snowfake: PHP 64-bit is required");
            }

            if ($node < 0 || $node > 1023) {
                throw new InvalidArgumentException("Tokens::snowfake: node must be between 0 and 1023");
            }

            $epoch = 1735689600000; // 2025-01-01T00:00:00Z
            $now = (int) floor(microtime(true) * 1000);
            $ts = max(0, $now - $epoch) & ((1 << 41) - 1);
            $seq = random_int(0, (1 << 12) - 1);

            return ($ts << 22) | ($node << 12) | $seq;
        }
    }
