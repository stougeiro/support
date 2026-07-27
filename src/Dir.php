<?php declare(strict_types=1);

    namespace STDW\Support;


    final class Dir
    {
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

        public static function absolute_path(string $path): string
        {
            $path = str_replace('\\', '/', $path); // Normaliza separadores
            $path = preg_replace('/\/+/', '/', $path); // Remove múltiplos separadores
            $parts = explode('/', $path);
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
