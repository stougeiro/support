<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Array utilities helper.
     *
     * Provides lightweight, strictly‑typed helpers for working with arrays.
     * Focuses on safe normalization, wrapping, and inspection operations.
     * All methods are static and designed for use in low‑level support code.
     */
    final class Arr
    {
        /**
         * @param array<mixed> $array
         * @return bool
         */
        public static function empty(array $array): bool
        {
            return $array === []; 
        }

        /**
         * @param array<mixed> $array
         * @return array<mixed>|null
         */
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

        /**
         * @param array<mixed> $array
         * @return array<mixed>|null
         */
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

        /**
         * @param string|int $key
         * @param array<mixed> $array
         * @return mixed
         */
        public static function grab(string|int $key, array &$array): mixed
        {
            if ( ! array_key_exists($key, $array)) {
                return null;
            }

            $item = $array[$key];

            unset($array[$key]);

            return $item;
        }

        /**
         * @param mixed $item
         * @return array<mixed>
         */
        public static function wrap(mixed $item): array
        {
            if (is_null($item)) {
                return [];
            }

            return is_array($item) ? $item : [$item];
        }

        /**
         * @param array<int|string> $keys 
         * @param array<mixed> $array 
         * @return bool
         */
        public static function keysExists(array $keys, array $array): bool
        {
            foreach ($keys as $key) {
                if ( ! array_key_exists($key, $array)) {
                    return false;
                }
            }

            return true;
        }

        /**
         * @param array<mixed> $array 
         * @param array<int|string> $keys 
         * @return array<mixed> 
         */
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

        /**
         * @param array<mixed> $array 
         * @param array<int|string> $keys 
         * @return array<mixed> 
         */
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

        /**
         * @param array<mixed> $array
         * @return array<mixed>
         */
        public static function flatten(array $array): array
        {
            $result = [];

            array_walk_recursive($array, function($value) use (&$result) {
                $result[] = $value;
            });

            return $result;
        }
    }
