<?php declare(strict_types=1);

    namespace STDW\Support;


    final class Dir
    {
        public static function rrmdir(string $dir): bool
        {
            $items = array_diff(scandir($dir), ['.','..']);

            foreach ($items as $item) {
                (is_dir($dir .DIRECTORY_SEPARATOR. $item)) ? static::rrmdir($dir .DIRECTORY_SEPARATOR. $item) : unlink($dir .DIRECTORY_SEPARATOR. $item);
            }

            return rmdir($dir);
        }
    }