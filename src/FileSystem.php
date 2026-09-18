<?php declare(strict_types=1);

    namespace STDW\Support;

    use RecursiveDirectoryIterator;
    use RecursiveIteratorIterator;
    use SplFileInfo;


    /**
     * Filesystem helper.
     *
     * Provides unified utilities for inspecting, normalizing, and operating
     * on files and directories. Handles both single files and recursive
     * directory operations transparently.
     * All methods are static and intended for low-level filesystem support.
     */
    final class FileSystem
    {
        // ---------------------------------------------------------------
        // Path
        // ---------------------------------------------------------------

        /**
         * Normalizes a path to absolute form without touching the filesystem.
         *
         * Resolves "..", ".", and multiple slashes. Does not resolve symlinks
         * or check if the path exists — purely string-based normalization.
         * Works for both files and directories.
         * Complexity: O(n) where n = path segments.
         *
         * @param string $path
         * @return string
         */
        public static function absolute(string $path): string
        {
            $path = str_replace('\\', '/', $path);
            $path = preg_replace('/\/+/', '/', $path);
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

        // ---------------------------------------------------------------
        // Size
        // ---------------------------------------------------------------

        /**
         * Returns the size of a file or directory in bytes.
         *
         * For files: returns filesize() directly.
         * For directories: recursively iterates all files and sums sizes.
         * Returns 0 if the path does not exist.
         * Complexity: O(n) for directories where n = total files.
         *
         * @param string $path
         * @return int
         */
        public static function size(string $path): int
        {
            if (is_file($path)) {
                return (int) filesize($path);
            }

            if ( ! is_dir($path)) {
                return 0;
            }

            $size = 0;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            /** @var SplFileInfo $item */
            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    $size += $item->getSize();
                }
            }

            return $size;
        }

        /**
         * Converts bytes to a human-readable string.
         *
         * Uses binary units (1024-based): B, KB, MB, GB, TB.
         * Complexity: O(1).
         *
         * @param int $bytes
         * @return string
         */
        public static function unit(int $bytes): string
        {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];

            $bytes = max(0, $bytes);
            $power = $bytes > 0 ? (int) min(floor(log($bytes, 1024)), count($units) - 1) : 0;
            $value = $bytes / (1024 ** $power);

            return sprintf('%.2f %s', $value, $units[$power]);
        }

        // ---------------------------------------------------------------
        // CRUD
        // ---------------------------------------------------------------

        /**
         * Creates a directory recursively.
         *
         * Idempotent: returns true if the directory already exists.
         * Creates all parent directories as needed.
         * Complexity: O(d) where d = directory depth.
         *
         * @param string $path
         * @param int $permissions
         * @return bool
         */
        public static function mkdir(string $path, int $permissions = 0755): bool
        {
            if (is_dir($path)) {
                return true;
            }

            return mkdir($path, $permissions, true);
        }

        /**
         * Recursively removes a directory and all its contents.
         *
         * Uses RecursiveIteratorIterator (CHILD_FIRST) for iterative
         * deletion — no recursion, no stack overflow risk.
         * Returns false if the path is not a directory.
         * Complexity: O(n) where n = total files and subdirectories.
         *
         * @param string $directory
         * @return bool
         */
        public static function rrmdir(string $directory): bool
        {
            if ( ! is_dir($directory)) {
                return false;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            /** @var SplFileInfo $item */
            foreach ($iterator as $item) {
                if ($item->isDir()) {
                    rmdir($item->getRealPath());
                } else {
                    unlink($item->getRealPath());
                }
            }

            return rmdir($directory);
        }

        /**
         * Copies a file or directory to a new location.
         *
         * For files: uses native copy().
         * For directories: recursively copies all contents, preserving
         * directory structure. Creates destination if it doesn't exist.
         * Returns false if source does not exist or copy fails.
         * Complexity: O(n) where n = total files.
         *
         * @param string $source
         * @param string $destination
         * @return bool
         */
        public static function copy(string $source, string $destination): bool
        {
            if (is_file($source)) {
                $parentDir = dirname($destination);

                if ( ! is_dir($parentDir)) {
                    mkdir($parentDir, 0755, true);
                }

                return copy($source, $destination);
            }

            if ( ! is_dir($source)) {
                return false;
            }

            if ( ! is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            /** @var SplFileInfo $item */
            foreach ($iterator as $item) {
                /** @var RecursiveDirectoryIterator $inner */
                $inner = $iterator->getInnerIterator();
                $target = $destination . '/' . $inner->getSubPathname();

                if ($item->isDir()) {
                    if ( ! is_dir($target)) {
                        mkdir($target, 0755, true);
                    }
                } else {
                    $parentDir = dirname($target);

                    if ( ! is_dir($parentDir)) {
                        mkdir($parentDir, 0755, true);
                    }

                    if ( ! copy($item->getRealPath(), $target)) {
                        return false;
                    }
                }
            }

            return true;
        }

        // ---------------------------------------------------------------
        // Find
        // ---------------------------------------------------------------

        /**
         * Recursively globs files matching a pattern.
         *
         * Traverses all subdirectories to find matches. Uses array spread
         * instead of array_merge in loop for O(n) performance.
         * Complexity: O(n) where n = total files in directory tree.
         *
         * @param string $pattern
         * @param int $flags
         * @return array<int, string>
         */
        public static function rglob(string $pattern, int $flags = 0): array
        {
            $files = glob($pattern, $flags);
            $files = $files === false ? [] : $files;

            $directories = glob(
                dirname($pattern) . DIRECTORY_SEPARATOR . '*',
                GLOB_ONLYDIR | GLOB_NOSORT
            );

            $directories = $directories === false ? [] : $directories;

            foreach ($directories as $dir) {
                $files = [
                    ...$files,
                    ...self::rglob($dir . DIRECTORY_SEPARATOR . basename($pattern), $flags),
                ];
            }

            return $files;
        }

        // ---------------------------------------------------------------
        // Info
        // ---------------------------------------------------------------

        /**
         * Returns the MIME type of a file.
         *
         * Uses finfo_file() with fallback to mime_content_type().
         * Returns 'application/octet-stream' if detection fails.
         * Complexity: O(1).
         *
         * @param string $file
         * @return string
         */
        public static function mime(string $file): string
        {
            if ( ! is_file($file)) {
                return 'application/octet-stream';
            }

            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);

                if ($finfo !== false) {
                    $mime = finfo_file($finfo, $file);
                    finfo_close($finfo);

                    if ($mime !== false) {
                        return $mime;
                    }
                }
            }

            if (function_exists('mime_content_type')) {
                $mime = mime_content_type($file);

                if ($mime !== false) {
                    return $mime;
                }
            }

            return 'application/octet-stream';
        }
    }
