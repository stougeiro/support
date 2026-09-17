<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Array utilities helper.
     *
     * Provides lightweight, strictly-typed helpers for working with arrays.
     * Focuses on safe normalization, wrapping, inspection, and polyfills for
     * PHP 8.4+ functions. All methods are static and designed for use in
     * low-level support code.
     */
    final class Arr
    {
        /**
         * Checks whether the given array is empty.
         *
         * Typed wrapper for `$array === []`.
         * Guarantees the input is an array before comparison.
         *
         * @param array<mixed> $array
         * @return bool
         */
        public static function empty(array $array): bool
        {
            return $array === [];
        }

        /**
         * Removes and returns the first element of the array (by reference).
         *
         * Equivalent to a "queue shift". Uses array_key_first() (PHP 7.3+)
         * to retrieve the key without iterating. Returns null if empty.
         *
         * @param array<mixed> $array
         * @return array<mixed>|null
         */
        public static function kshift(array &$array): array|null
        {
            if (self::empty($array)) {
                return null;
            }

            $key = array_key_first($array);
            $item = array($key => $array[$key]);

            unset($array[$key]);

            return $item;
        }

        /**
         * Removes and returns the last element of the array (by reference).
         *
         * Equivalent to a "stack pop". Uses array_key_last() (PHP 7.3+)
         * to retrieve the key without iterating. Returns null if empty.
         *
         * @param array<mixed> $array
         * @return array<mixed>|null
         */
        public static function kpop(array &$array): array|null
        {
            if (self::empty($array)) {
                return null;
            }

            $key = array_key_last($array);
            $item = array($key => $array[$key]);

            unset($array[$key]);

            return $item;
        }

        /**
         * Removes and returns the value of a specific key (by reference).
         *
         * Uses array_key_exists() instead of isset() because isset() returns
         * false when the value is null. Here the key may exist with a null
         * value and should still be returned.
         *
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
         * Wraps a value in an array.
         *
         * If the value is already an array, returns it unchanged.
         * If null, returns an empty array.
         * Otherwise, wraps it in [$item].
         * Useful for normalizing inputs that may be single-value or array.
         *
         * @param mixed $item
         * @return array<mixed>
         */
        public static function wrap(mixed $item): array
        {
            if ($item === null) {
                return [];
            }

            return is_array($item) ? $item : [$item];
        }

        /**
         * Checks whether all given keys exist in the array.
         *
         * Uses array_diff_key() (C-level) to remove the desired keys.
         * If the result is empty, all keys existed.
         * Complexity: O(n + k) where n = array size, k = number of keys.
         *
         * @param array<int|string> $keys
         * @param array<mixed> $array
         * @return bool
         */
        public static function keysExists(array $keys, array $array): bool
        {
            return array_diff_key($array, array_flip($keys)) === [];
        }

        /**
         * Returns a new array containing only the given keys.
         *
         * Uses array_intersect_key() (C-level) with array_flip() for
         * O(1) key lookup. Preserves original array order.
         *
         * @param array<mixed> $array
         * @param array<int|string> $keys
         * @return array<mixed>
         */
        public static function only(array $array, array $keys): array
        {
            return array_intersect_key($array, array_flip($keys));
        }

        /**
         * Returns a new array excluding the given keys.
         *
         * Uses array_diff_key() (C-level) with array_flip() for
         * efficient removal. Preserves original array order.
         *
         * @param array<mixed> $array
         * @param array<int|string> $keys
         * @return array<mixed>
         */
        public static function except(array $array, array $keys): array
        {
            return array_diff_key($array, array_flip($keys));
        }

        /**
         * Flattens an array into a single dimension.
         *
         * Preserves only leaf values (non-arrays). Keys are discarded.
         * Iterative implementation with explicit stack — no recursion,
         * no stack overflow risk regardless of nesting depth.
         * O(n) where n = total leaf values.
         *
         * @param array<mixed> $array
         * @return array<mixed>
         */
        public static function flatten(array $array): array
        {
            $result = [];
            $stack = [$array];

            while ($stack !== []) {
                $current = array_pop($stack);

                foreach ($current as $value) {
                    if (is_array($value)) {
                        $stack[] = $value;
                    } else {
                        $result[] = $value;
                    }
                }
            }

            return $result;
        }


        // ---------------------------------------------------------------
        // Polyfills — PHP 8.4
        //
        // Equivalent implementations of PHP 8.4 native functions.
        // Short-circuit: stops at the first deterministic result.
        // Callback receives ($value, $key) — same signature as PHP 8.4.
        // ---------------------------------------------------------------

        /**
         * Polyfill for PHP 8.4's array_any().
         *
         * Returns true if the callback returns true for any element.
         * Short-circuit: stops at the first match.
         *
         * @param array<mixed> $array
         * @param callable(mixed, int|string): bool $callback
         * @return bool
         */
        public static function any(array $array, callable $callback): bool
        {
            foreach ($array as $key => $value) {
                if ($callback($value, $key) === true) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Polyfill for PHP 8.4's array_all().
         *
         * Returns true if the callback returns true for all elements.
         * Returns true for empty arrays (vacuous truth).
         * Short-circuit: stops at the first false.
         *
         * @param array<mixed> $array
         * @param callable(mixed, int|string): bool $callback
         * @return bool
         */
        public static function all(array $array, callable $callback): bool
        {
            foreach ($array as $key => $value) {
                if ($callback($value, $key) !== true) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Polyfill for PHP 8.4's array_find().
         *
         * Returns the value of the first element whose callback returns true.
         * Returns null if no element satisfies the callback.
         * Short-circuit: stops at the first match.
         *
         * @param array<mixed> $array
         * @param callable(mixed, int|string): bool $callback
         * @return mixed|null
         */
        public static function find(array $array, callable $callback): mixed
        {
            foreach ($array as $key => $value) {
                if ($callback($value, $key) === true) {
                    return $value;
                }
            }

            return null;
        }

        /**
         * Polyfill for PHP 8.4's array_find_key().
         *
         * Returns the key of the first element whose callback returns true.
         * Returns null if no element satisfies the callback.
         * Short-circuit: stops at the first match.
         *
         * @param array<mixed> $array
         * @param callable(mixed, int|string): bool $callback
         * @return int|string|null
         */
        public static function findKey(array $array, callable $callback): int|string|null
        {
            foreach ($array as $key => $value) {
                if ($callback($value, $key) === true) {
                    return $key;
                }
            }

            return null;
        }


        // ---------------------------------------------------------------
        // Polyfills — PHP 8.5
        //
        // Equivalent implementations of PHP 8.5 native functions.
        // No callback — strictly follows the PHP spec.
        // Uses array_key_first/last (PHP 7.3+) for O(1).
        // ---------------------------------------------------------------

        /**
         * Polyfill for PHP 8.5's array_first().
         *
         * Returns the first value of the array (in internal pointer order).
         * Returns null if the array is empty.
         * Complexity: O(1).
         *
         * @param array<mixed> $array
         * @return mixed|null
         */
        public static function first(array $array): mixed
        {
            return $array === [] ? null : $array[array_key_first($array)];
        }

        /**
         * Polyfill for PHP 8.5's array_last().
         *
         * Returns the last value of the array (in internal pointer order).
         * Returns null if the array is empty.
         * Complexity: O(1).
         *
         * @param array<mixed> $array
         * @return mixed|null
         */
        public static function last(array $array): mixed
        {
            return $array === [] ? null : $array[array_key_last($array)];
        }


        // ---------------------------------------------------------------
        // Custom helpers
        // ---------------------------------------------------------------

        /**
         * Flattens a nested array into dot notation.
         *
         * Converts ['a' => ['b' => ['c' => 1]]] into ['a.b.c' => 1].
         * Useful for normalizing configs, nested data, etc.
         * Recursive — uses direct assignment (O(1) amortized) instead
         * of array_merge in a loop (O(n^2) worst case).
         *
         * @param array<mixed> $array
         * @param string $prefix
         * @return array<string, mixed>
         */
        public static function dot(array $array, string $prefix = ''): array
        {
            $result = [];

            foreach ($array as $key => $value) {
                $newKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;

                if (is_array($value) && $value !== []) {
                    foreach (self::dot($value, $newKey) as $k => $v) {
                        $result[$k] = $v;
                    }
                } else {
                    $result[$newKey] = $value;
                }
            }

            return $result;
        }

        /**
         * Converts a dot notation array back into a nested array.
         *
         * Converts ['a.b.c' => 1] into ['a' => ['b' => ['c' => 1]]].
         * Uses a numeric index instead of array_slice() to avoid
         * extra allocations at each recursion level.
         *
         * @param array<string, mixed> $array
         * @return array<mixed>
         */
        public static function undot(array $array): array
        {
            $result = [];

            foreach ($array as $key => $value) {
                $parts = explode('.', $key);
                $result = self::undotSet($result, $parts, 0, $value);
            }

            return $result;
        }

        /**
         * Iterative helper for undot(). Inserts a value into a nested
         * array using a reference chain to traverse the keys.
         * No recursion, no stack overflow risk.
         *
         * @param array<mixed> $array
         * @param list<string> $keys
         * @param int $index
         * @param mixed $value
         * @return array<mixed>
         */
        private static function undotSet(array $array, array $keys, int $index, mixed $value): array
        {
            $current = &$array;

            for ($i = $index, $max = count($keys) - 1; $i < $max; $i++) {
                $key = $keys[$i];

                if ( ! isset($current[$key]) || ! is_array($current[$key])) {
                    $current[$key] = [];
                }

                $current = &$current[$key];
            }

            $current[$keys[$max]] = $value;

            return $array;
        }

        /**
         * Returns N random key => value pairs from the array.
         *
         * Works with both lists (indexed) and dictionaries (associative).
         * Preserves original keys. If $count >= count($array), returns
         * the entire array shuffled.
         *
         * Uses Partial Fisher-Yates (O($count)) instead of rejection
         * sampling to avoid rejections when $count approaches array size.
         *
         * @param array<mixed> $array
         * @param int $count
         * @return array<mixed>
         */
        public static function random(array $array, int $count = 1): array
        {
            if ($array === []) {
                return [];
            }

            $count = max(1, $count);
            $keys = array_keys($array);
            $total = count($keys);

            if ($count >= $total) {
                /** Full shuffle: native shuffle() is C-level, faster than manual Fisher-Yates
                 */
                shuffle($keys);

                $result = [];

                foreach ($keys as $key) {
                    $result[$key] = $array[$key];
                }

                return $result;
            }

            /** Partial Fisher-Yates: select $count elements, O($count)
             */
            for ($i = 0; $i < $count; $i++) {
                $j = random_int($i, $total - 1);
                [$keys[$i], $keys[$j]] = [$keys[$j], $keys[$i]];
            }

            $result = [];

            for ($i = 0; $i < $count; $i++) {
                $result[$keys[$i]] = $array[$keys[$i]];
            }

            return $result;
        }
    }
