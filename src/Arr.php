<?php declare(strict_types=1);

    namespace STDW\Support;


    final class Arr
    {
        public static function empty(array $array): bool
        {
            return $array === [];
        }

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

            return is_array($item) ? $item : [$item];
        }

        public static function keysExists(array $keys, array $array): bool
        {
            foreach ($keys as $key) {
                if ( ! array_key_exists($key, $array)) {
                    return false;
                }
            }

            return true;
        }

        public static function only(array $array, array $keys): array
        {
            $keys = array_flip($keys);
            $result = [];

            foreach ($array as $key => $value) {
                if (isset($keys[$key])) {
                    $result[$key] = $value;
                }
            }

            return $result;
        }

        public static function except(array $array, array $keys): array
        {
            $keys = array_flip($keys);
            $result = [];

            foreach ($array as $key => $value) {
                if ( ! isset($keys[$key])) {
                    $result[$key] = $value;
                }
            }

            return $result;
        }

        public static function flatten(array $array): array
        {
            $result = [];

            array_walk_recursive($array, function($value) use (&$result) {
                $result[] = $value;
            });

            return $result;
        }
    }
