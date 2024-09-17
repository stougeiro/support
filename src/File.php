<?php declare(strict_types=1);

    namespace STDW\Support;


    final class File
    {
        public static function rglob(string $pattern, int $flags = 0): array
        {
            $files = glob($pattern, $flags);
    
            foreach (glob( dirname($pattern) .DIRECTORY_SEPARATOR. '*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
                $files = array_merge($files, static::rglob($dir .DIRECTORY_SEPARATOR. basename($pattern), $flags));
            }
    
            return $files;
        }

        public static function unit(int $size): string
        {
            if ( ! is_int($size)) {
                return 0;
            }

            $units = ['B', 'KB', 'MB', 'GB'];
            $power = $size > 0 ? floor(log($size, 1024)) : 0;

            return ceil( (float) number_format($size / pow(1024, $power), 2, '.', ',')) .' '. $units[$power];
        }
    
        public static function filesize(string $file): string
        {
            if ( ! is_file($file)) {
                return 0;
            }

            return static::unit( filesize($file));
        }
    }