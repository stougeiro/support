<?php declare(strict_types=1);

    namespace STDW\Support;


    final class Password
    {
        private static int $cost = 12;


        public static function hash(string $password): string
        {
            return password_hash($password, PASSWORD_BCRYPT, [
                'cost' => static::$cost,
            ]);
        }

        public static function verify(string $password, string $hash): bool
        {
            return password_verify($password, $hash);
        }
    }