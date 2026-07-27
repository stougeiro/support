<?php declare(strict_types=1);

    namespace STDW\Support;


    final class File
    {
        public static function rglob(string $pattern, int $flags = 0): array
        {
            $files = glob($pattern, $flags);
    
            foreach (glob( dirname($pattern) .DIRECTORY_SEPARATOR. '*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
                $files = array_merge(
                    $files,
                    static::rglob($dir .DIRECTORY_SEPARATOR. basename($pattern), $flags)
                );
            }
    
            return $files;
        }

        public static function unit(int $size): string
        {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $size = max(0, $size);
            $power = $size > 0 ? min(floor(log($size, 1024)), count($units) - 1) : 0;
            $value = $size / (1024 ** $power);

            return sprintf('%.2f %s', $value, $units[$power]);
        }
    
        public static function size(string $file): string
        {
            if ( ! is_file($file)) {
                return '0 B';
            }

            return static::unit( filesize($file));
        }
    }
