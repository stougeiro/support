<?php

use STDW\Support\FileSystem;

// ---------------------------------------------------------------
// absolute()
// ---------------------------------------------------------------

test('absolute resolves relative path', function () {
    expect(FileSystem::absolute('foo/bar'))->toBe('/foo/bar');
});

test('absolute resolves dot-dot', function () {
    expect(FileSystem::absolute('foo/bar/../baz'))->toBe('/foo/baz');
});

test('absolute resolves dot', function () {
    expect(FileSystem::absolute('foo/./bar'))->toBe('/foo/bar');
});

test('absolute removes multiple slashes', function () {
    expect(FileSystem::absolute('foo///bar'))->toBe('/foo/bar');
});

test('absolute normalizes backslashes', function () {
    expect(FileSystem::absolute('foo\\bar'))->toBe('/foo/bar');
});

test('absolute handles trailing slash', function () {
    expect(FileSystem::absolute('foo/bar/'))->toBe('/foo/bar');
});

test('absolute handles root', function () {
    expect(FileSystem::absolute('/'))->toBe('/');
});

test('absolute handles deep dot-dot', function () {
    expect(FileSystem::absolute('a/b/c/../../d'))->toBe('/a/d');
});

// ---------------------------------------------------------------
// size()
// ---------------------------------------------------------------

test('size returns file size in bytes', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_size_file_' . uniqid();
    file_put_contents($tmp, 'hello'); // 5 bytes

    expect(FileSystem::size($tmp))->toBe(5);

    unlink($tmp);
});

test('size returns 0 for empty directory', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_size_empty_' . uniqid();
    mkdir($tmp);

    expect(FileSystem::size($tmp))->toBe(0);

    rmdir($tmp);
});

test('size returns correct total for directory', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_size_dir_' . uniqid();
    mkdir($tmp);
    file_put_contents($tmp . '/a.txt', 'hello'); // 5 bytes
    file_put_contents($tmp . '/b.txt', 'world!'); // 6 bytes

    expect(FileSystem::size($tmp))->toBe(11);

    FileSystem::rrmdir($tmp);
});

test('size returns 0 for non-existent path', function () {
    expect(FileSystem::size('/nonexistent/path/xyz'))->toBe(0);
});

test('size includes subdirectories recursively', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_size_sub_' . uniqid();
    mkdir($tmp);
    mkdir($tmp . '/sub');
    file_put_contents($tmp . '/sub/file.txt', 'data'); // 4 bytes

    expect(FileSystem::size($tmp))->toBe(4);

    FileSystem::rrmdir($tmp);
});

// ---------------------------------------------------------------
// unit()
// ---------------------------------------------------------------

test('unit formats bytes', function () {
    expect(FileSystem::unit(0))->toBe('0.00 B');
    expect(FileSystem::unit(100))->toBe('100.00 B');
    expect(FileSystem::unit(1024))->toBe('1.00 KB');
    expect(FileSystem::unit(1048576))->toBe('1.00 MB');
    expect(FileSystem::unit(1073741824))->toBe('1.00 GB');
    expect(FileSystem::unit(1099511627776))->toBe('1.00 TB');
});

test('unit handles negative values', function () {
    expect(FileSystem::unit(-100))->toBe('0.00 B');
});

// ---------------------------------------------------------------
// mkdir()
// ---------------------------------------------------------------

test('mkdir creates new directory', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_mkdir_' . uniqid();

    expect(FileSystem::mkdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeTrue();

    FileSystem::rrmdir($tmp);
});

test('mkdir creates nested directory', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_mkdir_nested_' . uniqid() . '/a/b/c';

    expect(FileSystem::mkdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeTrue();

    FileSystem::rrmdir(sys_get_temp_dir() . '/fs_test_mkdir_nested_' . uniqid());
});

test('mkdir returns true if already exists', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_mkdir_exists_' . uniqid();
    mkdir($tmp);

    expect(FileSystem::mkdir($tmp))->toBeTrue();

    rmdir($tmp);
});

// ---------------------------------------------------------------
// rrmdir()
// ---------------------------------------------------------------

test('rrmdir removes directory with files', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_rrmdir_' . uniqid();
    mkdir($tmp);
    file_put_contents($tmp . '/file1.txt', 'a');
    file_put_contents($tmp . '/file2.txt', 'b');

    expect(FileSystem::rrmdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeFalse();
});

test('rrmdir removes directory with subdirectories', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_rrmdir_sub_' . uniqid();
    mkdir($tmp);
    mkdir($tmp . '/sub');
    file_put_contents($tmp . '/sub/file.txt', 'c');

    expect(FileSystem::rrmdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeFalse();
});

test('rrmdir removes empty directory', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_rrmdir_empty_' . uniqid();
    mkdir($tmp);

    expect(FileSystem::rrmdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeFalse();
});

test('rrmdir returns false for non-existent directory', function () {
    expect(FileSystem::rrmdir('/nonexistent/path/xyz'))->toBeFalse();
});

// ---------------------------------------------------------------
// copy() — file
// ---------------------------------------------------------------

test('copy copies single file', function () {
    $src = sys_get_temp_dir() . '/fs_test_copy_file_' . uniqid();
    $dst = sys_get_temp_dir() . '/fs_test_copy_file_dst_' . uniqid();
    file_put_contents($src, 'hello');

    expect(FileSystem::copy($src, $dst))->toBeTrue();
    expect(file_get_contents($dst))->toBe('hello');

    unlink($src);
    unlink($dst);
});

test('copy creates parent directories for file', function () {
    $src = sys_get_temp_dir() . '/fs_test_copy_file_parent_' . uniqid();
    $dst = sys_get_temp_dir() . '/fs_test_copy_file_parent_dst_' . uniqid() . '/sub/file.txt';
    file_put_contents($src, 'data');

    expect(FileSystem::copy($src, $dst))->toBeTrue();
    expect(file_get_contents($dst))->toBe('data');

    unlink($src);
    FileSystem::rrmdir(dirname(dirname($dst)));
});

// ---------------------------------------------------------------
// copy() — directory
// ---------------------------------------------------------------

test('copy copies directory recursively', function () {
    $src = sys_get_temp_dir() . '/fs_test_copy_dir_' . uniqid();
    $dst = sys_get_temp_dir() . '/fs_test_copy_dir_dst_' . uniqid();
    mkdir($src);
    mkdir($src . '/sub');
    file_put_contents($src . '/file.txt', 'hello');
    file_put_contents($src . '/sub/file2.txt', 'world');

    expect(FileSystem::copy($src, $dst))->toBeTrue();
    expect(is_dir($dst))->toBeTrue();
    expect(is_dir($dst . '/sub'))->toBeTrue();
    expect(file_get_contents($dst . '/file.txt'))->toBe('hello');
    expect(file_get_contents($dst . '/sub/file2.txt'))->toBe('world');

    FileSystem::rrmdir($src);
    FileSystem::rrmdir($dst);
});

test('copy returns false for non-existent source', function () {
    expect(FileSystem::copy('/nonexistent/path', '/tmp/dest'))->toBeFalse();
});

test('copy preserves file contents exactly', function () {
    $src = sys_get_temp_dir() . '/fs_test_copy_exact_src_' . uniqid();
    $dst = sys_get_temp_dir() . '/fs_test_copy_exact_dst_' . uniqid();
    $content = str_repeat('x', 1024);
    file_put_contents($src, $content);

    FileSystem::copy($src, $dst);

    expect(file_get_contents($dst))->toBe($content);

    unlink($src);
    unlink($dst);
});

// ---------------------------------------------------------------
// rglob()
// ---------------------------------------------------------------

test('rglob finds files in directory', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_rglob_' . uniqid();
    mkdir($tmp);
    file_put_contents($tmp . '/a.txt', '1');
    file_put_contents($tmp . '/b.txt', '2');

    $result = FileSystem::rglob($tmp . '/*.txt');

    expect($result)->toHaveCount(2);

    FileSystem::rrmdir($tmp);
});

test('rglob finds files recursively', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_rglob_rec_' . uniqid();
    mkdir($tmp);
    mkdir($tmp . '/sub');
    file_put_contents($tmp . '/a.txt', '1');
    file_put_contents($tmp . '/sub/b.txt', '2');

    $result = FileSystem::rglob($tmp . '/*.txt');

    expect($result)->toHaveCount(2);

    FileSystem::rrmdir($tmp);
});

test('rglob returns empty array for no matches', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_rglob_empty_' . uniqid();
    mkdir($tmp);

    $result = FileSystem::rglob($tmp . '/*.xyz');

    expect($result)->toBe([]);

    rmdir($tmp);
});

// ---------------------------------------------------------------
// mime()
// ---------------------------------------------------------------

test('mime detects text file', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_mime_txt_' . uniqid() . '.txt';
    file_put_contents($tmp, 'hello');

    $mime = FileSystem::mime($tmp);

    expect($mime)->toBe('text/plain');

    unlink($tmp);
});

test('mime detects php file', function () {
    $tmp = sys_get_temp_dir() . '/fs_test_mime_php_' . uniqid() . '.php';
    file_put_contents($tmp, '<?php echo "hi";');

    $mime = FileSystem::mime($tmp);

    expect($mime)->toContain('php');

    unlink($tmp);
});

test('mime returns default for non-existent file', function () {
    expect(FileSystem::mime('/nonexistent/file.txt'))->toBe('application/octet-stream');
});
