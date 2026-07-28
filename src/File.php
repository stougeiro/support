<?php declare(strict_types=1);

    namespace STDW\Support;


    /**
     * File helper.
     *
     * Provides lightweight utilities for inspecting, normalizing, and resolving
     * filesystem file paths. Focuses on safe existence checks, extension handling,
     * and consistent path operations across environments.
     * All methods are static and intended for low‑level filesystem support.
     */
    final class File
    {
        /**
         * @param string $pattern 
         * @param int $flags 
         * @return array<mixed>
         */
        public static function rglob(string $pattern, int $flags = 0): array
        {
            $files = glob($pattern, $flags);
            $files = $files === false ? [] : $files;

            $directories = glob( dirname($pattern) .DIRECTORY_SEPARATOR. '*', GLOB_ONLYDIR|GLOB_NOSORT);
            $directories = $directories === false ? [] : $directories;
    
            foreach ($directories as $dir) {
                $files = array_merge(
                    $files,
                    static::rglob($dir .DIRECTORY_SEPARATOR. basename($pattern), $flags)
                );
            }
    
            return $files;
        }

        /**
         * @param int $size 
         * @return string 
         */
        public static function unit(int $size): string
        {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];

            $size = (int) max(0, $size);
            $power = $size > 0 ? (int) min(floor(log($size, 1024)), count($units) - 1) : 0;
            $value = $size / (1024 ** $power);

            return sprintf('%.2f %s', $value, $units[$power]);
        }
    
        /**
         * @param string $file 
         * @return string 
         */
        public static function size(string $file): string
        {
            if ( ! is_file($file)) {
                return '0 B';
            }

            return static::unit( (int) filesize($file));
        }
    }
