<?php declare(strict_types=1);

    namespace STDW\Support;


    final class Password
    {
        public static function hash(string $password, int $cost = 12): string
        {
            return password_hash($password, PASSWORD_BCRYPT, [
                'cost' => $cost,
            ]);
        }

        public static function verify(string $password, string $hash): bool
        {
            return password_verify($password, $hash);
        }

        public static function rehash(string $hash, int $cost = 12): bool
        {
            return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => $cost]);
        }
    }
