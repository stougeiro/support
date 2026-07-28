<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * Directory path helper.
     *
     * Provides utilities for normalizing, resolving, and validating directory paths.
     * Focuses on safe canonicalization, removing redundant segments, and producing
     * consistent absolute paths across different environments.
     * All methods are static and intended for low‑level filesystem support.
     */
    final class Dir
    {
        /**
         * @param string $dir 
         * @return bool 
         */
        public static function rrmdir(string $dir): bool
        {
            if ( ! is_dir($dir)) {
                return false;
            }

            $items = scandir($dir);

            if ($items === false) {
                return false;
            }

            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $path = "$dir/$item";

                if (is_dir($path)) {
                    static::rrmdir($path);
                } else {
                    unlink($path);
                }
            }

            return rmdir($dir);
        }

        /**
         * @param string $path 
         * @return string 
         */
        public static function absolute_path(string $path): string
        {
            $path = str_replace('\\', '/', $path); // Normaliza separadores
            $path = preg_replace('/\/+/', '/', $path); // Remove múltiplos separadores
            $parts = explode('/', (string) $path);
            $absolutes = [];

            foreach ($parts as $part) {
                if ($part === '' || $part === '.') {
                    continue;
                }

                if ($part === '..') {
                    array_pop($absolutes);

                    continue;
                }

                $absolutes[] = $part;
            }

            return '/' . implode('/', $absolutes);
        }
    }
