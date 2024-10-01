<?php declare(strict_types=1);

    namespace STDW\Support;


    final class Arr
    {
        public static function kshift(array &$array): array|null
        {
            if (static::empty($array)) {
                return null;
            }

            $key = array_key_first($array);
            $item = array($key => $array[$key]);

            unset($array[$key]);

            return $item;
        }

        public static function kpop(array &$array): array|null
        {
            if (static::empty($array)) {
                return null;
            }

            $key = array_key_last($array);
            $item = array($key => $array[$key]);

            unset($array[$key]);

            return $item;
        }

        public static function grab(string|int $key, array &$array): mixed
        {
            if ( ! array_key_exists($key, $array)) {
                return null;
            }

            $item = $array[$key];

            unset($array[$key]);

            return $item;
        }

        public static function wrap(array|string|null $item): array
        {
            if (is_null($item)) {
                return [];
            }

            return is_string($item) ? [$item] : $item;
        }

        public static function keysExists(array $keys, array $array): bool
        {
            return count( array_diff_key( array_flip($keys), $array)) == 0;
        }

        public static function fromString(string $text): array
        {
            $array = [];

            for($i=0; $i<strlen($text); $i++) {
                $array[] = $text[$i];
            }

            return $array;
        }

        public static function only(array $array, array $keys): array
        {
            return array_filter($array, function($key) use ($keys) {
                return in_array($key, $keys);
            }, ARRAY_FILTER_USE_KEY);
        }

        public static function empty(array $array): bool
        {
            return (empty($array) || $array == [] || count($array) == 0);
        }
    }