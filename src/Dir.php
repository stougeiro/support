<?php declare(strict_types=1);

    namespace STDW\Support;

    use RecursiveDirectoryIterator;
    use RecursiveIteratorIterator;
    use SplFileInfo;


    /**
     * Directory path helper.
     *
     * Provides utilities for creating, removing, copying, and inspecting
     * directory structures. Focuses on safe recursive operations and
     * consistent path normalization.
     * All methods are static and intended for low-level filesystem support.
     */
    final class Dir
    {
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
         * Normalizes a path to absolute form without touching the filesystem.
         *
         * Resolves "..", ".", and multiple slashes. Does not resolve symlinks
         * or check if the path exists — purely string-based normalization.
         * Complexity: O(n) where n = path segments.
         *
         * @param string $path
         * @return string
         */
        public static function absolutePath(string $path): string
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

        /**
         * Creates a directory recursively.
         *
         * Returns true if the directory already exists. Creates all
         * parent directories as needed. Default permissions: 0755.
         * Complexity: O(d) where d = directory depth.
         *
         * @param string $path
         * @param int $permissions
         * @return bool
         */
        public static function create(string $path, int $permissions = 0755): bool
        {
            if (is_dir($path)) {
                return true;
            }

            return mkdir($path, $permissions, true);
        }

        /**
         * Returns the total size of a directory in bytes.
         *
         * Iterates all files recursively and sums their sizes.
         * Returns 0 if the path is not a directory.
         * Complexity: O(n) where n = total files.
         *
         * @param string $path
         * @return int
         */
        public static function size(string $path): int
        {
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
         * Recursively copies a directory to a new location.
         *
         * Creates the destination if it doesn't exist. Copies file contents
         * (real copy, not symlinks). Preserves directory structure.
         * Returns false if source is not a directory or copy fails.
         * Complexity: O(n) where n = total files.
         *
         * @param string $source
         * @param string $destination
         * @return bool
         */
        public static function copy(string $source, string $destination): bool
        {
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
    }
