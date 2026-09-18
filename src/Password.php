<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Password helper.
     *
     * Provides utilities for hashing, verifying, upgrading, and validating
     * passwords. Supports bcrypt and argon2id algorithms. Includes strength
     * scoring and policy validation.
     * All methods are static and intended for low-level authentication support.
     */
    final class Password
    {
        /**
         * Hashes a password using the specified algorithm.
         *
         * Supports PASSWORD_BCRYPT and PASSWORD_ARGON2ID.
         * Complexity: O(1) — cost is controlled by algorithm parameters.
         *
         * @param string $password
         * @param string $algorithm
         * @param int $cost
         * @return string
         */
        public static function hash(string $password, string $algorithm = PASSWORD_BCRYPT, int $cost = 12): string
        {
            $options = match ($algorithm) {
                PASSWORD_ARGON2ID => [
                    'memory_cost' => 65536,
                    'time_cost' => 4,
                    'threads' => 1,
                ],

                default => ['cost' => $cost],
            };

            return password_hash($password, $algorithm, $options);
        }

        /**
         * Verifies a password against a hash.
         *
         * Complexity: O(1).
         *
         * @param string $password
         * @param string $hash
         * @return bool
         */
        public static function verify(string $password, string $hash): bool
        {
            return password_verify($password, $hash);
        }

        /**
         * Checks if a hash needs rehashing.
         *
         * Returns true if the hash was created with a different algorithm
         * or cost than the specified parameters.
         * Complexity: O(1).
         *
         * @param string $hash
         * @param string $algorithm
         * @param int $cost
         * @return bool
         */
        public static function needsRehash(string $hash, string $algorithm = PASSWORD_BCRYPT, int $cost = 12): bool
        {
            $options = match ($algorithm) {
                PASSWORD_ARGON2ID => [
                    'memory_cost' => 65536,
                    'time_cost' => 4,
                    'threads' => 1,
                ],

                default => ['cost' => $cost],
            };

            return password_needs_rehash($hash, $algorithm, $options);
        }

        /**
         * Upgrades a password hash if needed, returning the new hash.
         *
         * If the hash is already up-to-date, returns null.
         * If the password is invalid for the hash, returns null.
         * Useful for transparent hash upgrades on login.
         * Complexity: O(1).
         *
         * @param string $password
         * @param string $hash
         * @param string $algorithm
         * @param int $cost
         * @return string|null
         */
        public static function upgrade(string $password, string $hash, string $algorithm = PASSWORD_BCRYPT, int $cost = 12): ?string
        {
            if ( ! self::verify($password, $hash)) {
                return null;
            }

            if ( ! self::needsRehash($hash, $algorithm, $cost)) {
                return null;
            }

            return self::hash($password, $algorithm, $cost);
        }

        /**
         * Detects the algorithm used in a password hash.
         *
         * Returns 'bcrypt', 'argon2i', 'argon2id', or 'unknown'.
         * Complexity: O(1).
         *
         * @param string $hash
         * @return string
         */
        public static function algo(string $hash): string
        {
            $info = password_get_info($hash);

            return match ($info['algo']) {
                PASSWORD_BCRYPT => 'bcrypt',
                PASSWORD_ARGON2I => 'argon2i',
                PASSWORD_ARGON2ID => 'argon2id',

                default => 'unknown',
            };
        }

        /**
         * Returns a strength score from 0 (very weak) to 4 (very strong).
         *
         * Without rules: uses default policy (min 8, lowercase, uppercase, numbers, symbols).
         * With rules: merges with defaults, user rules take precedence.
         * If min rule fails, returns 0 immediately (hard fail).
         * Complexity: O(n) where n = password length.
         *
         * @param string $password
         * @param array{min?: int, lowercase?: bool, uppercase?: bool, numbers?: bool, symbols?: bool} $rules
         * @return int
         */
        public static function strength(string $password, array $rules = []): int
        {
            $min = $rules['min'] ?? 8;
            $lowercase = $rules['lowercase'] ?? true;
            $uppercase = $rules['uppercase'] ?? true;
            $numbers = $rules['numbers'] ?? true;
            $symbols = $rules['symbols'] ?? true;

            if (strlen($password) < $min) {
                return 0;
            }

            $checks = 0;
            $passed = 0;

            if ($lowercase) {
                $checks++;

                if (preg_match('/[a-z]/', $password)) {
                    $passed++;
                }
            }

            if ($uppercase) {
                $checks++;

                if (preg_match('/[A-Z]/', $password)) {
                    $passed++;
                }
            }

            if ($numbers) {
                $checks++;

                if (preg_match('/[0-9]/', $password)) {
                    $passed++;
                }
            }

            if ($symbols) {
                $checks++;

                if (preg_match('/[^A-Za-z0-9]/', $password)) {
                    $passed++;
                }
            }

            if ($checks === 0) {
                return 0;
            }

            return (int) ceil(($passed / $checks) * 4);
        }

        /**
         * Returns a human-readable label for a strength score.
         *
         * Accepts an optional translations array keyed by score (0-4).
         * Falls back to English defaults for missing keys.
         * Complexity: O(1).
         *
         * @param int $score
         * @param array<int, string> $labels
         * @return string
         */
        public static function strengthLabel(int $score, array $labels = []): string
        {
            $defaults = [
                0 => 'Very Weak',
                1 => 'Weak',
                2 => 'Fair',
                3 => 'Strong',
                4 => 'Very Strong',
            ];

            $labels = $labels + $defaults;

            return $labels[$score] ?? $defaults[0];
        }

        /**
         * Validates a password against a set of rules.
         *
         * Supported rules: min, uppercase, lowercase, numbers, symbols.
         * Returns true if all rules pass. Returns false if any rule fails.
         * Complexity: O(n) where n = password length.
         *
         * @param string $password
         * @param array{min?: int, lowercase?: bool, uppercase?: bool, numbers?: bool, symbols?: bool} $rules
         * @return bool
         */
        public static function validate(string $password, array $rules): bool
        {
            $length = strlen($password);

            if (isset($rules['min']) && $length < $rules['min']) {
                return false;
            }

            if (isset($rules['lowercase']) && $rules['lowercase'] && ! preg_match('/[a-z]/', $password)) {
                return false;
            }

            if (isset($rules['uppercase']) && $rules['uppercase'] && ! preg_match('/[A-Z]/', $password)) {
                return false;
            }

            if (isset($rules['numbers']) && $rules['numbers'] && ! preg_match('/[0-9]/', $password)) {
                return false;
            }

            if (isset($rules['symbols']) && $rules['symbols'] && ! preg_match('/[^A-Za-z0-9]/', $password)) {
                return false;
            }

            return true;
        }
    }
