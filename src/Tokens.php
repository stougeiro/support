<?php declare(strict_types=1);

    namespace STDW\Support;

    use RuntimeException;
    use InvalidArgumentException;


    final class Tokens
    {
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
                $out .= $alphabet[bindec(substr($bits, $i * 5, 5))];
            }

            return $out;
        }

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
