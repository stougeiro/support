<?php declare(strict_types=1);

    namespace STDW\Support;


    final class Dir
    {
        public static function rrmdir(string $_dir): bool
        {
            $items = array_diff(scandir($_dir), ['.','..']);

            foreach ($items as $item) {
                (is_dir($_dir .DIRECTORY_SEPARATOR. $item)) ? static::rrmdir($_dir .DIRECTORY_SEPARATOR. $item) : unlink($_dir .DIRECTORY_SEPARATOR. $item);
            }

            return rmdir($_dir);
        }
    }