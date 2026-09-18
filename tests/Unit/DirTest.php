<?php

use STDW\Support\Dir;

// ---------------------------------------------------------------
// rrmdir()
// ---------------------------------------------------------------

test('rrmdir removes directory with files', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_rrmdir_' . uniqid();
    mkdir($tmp);
    file_put_contents($tmp . '/file1.txt', 'a');
    file_put_contents($tmp . '/file2.txt', 'b');

    expect(Dir::rrmdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeFalse();
});

test('rrmdir removes directory with subdirectories', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_rrmdir_sub_' . uniqid();
    mkdir($tmp);
    mkdir($tmp . '/sub');
    file_put_contents($tmp . '/sub/file.txt', 'c');

    expect(Dir::rrmdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeFalse();
});

test('rrmdir removes empty directory', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_rrmdir_empty_' . uniqid();
    mkdir($tmp);

    expect(Dir::rrmdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeFalse();
});

test('rrmdir returns false for non-existent directory', function () {
    expect(Dir::rrmdir('/nonexistent/path/xyz'))->toBeFalse();
});

test('rrmdir returns false for non-directory path', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_rrmdir_file_' . uniqid();
    file_put_contents($tmp, 'not a dir');

    expect(Dir::rrmdir($tmp))->toBeFalse();

    unlink($tmp);
});

// ---------------------------------------------------------------
// absolutePath()
// ---------------------------------------------------------------

test('absolute_path resolves relative path', function () {
    expect(Dir::absolutePath('foo/bar'))->toBe('/foo/bar');
});

test('absolute_path resolves dot-dot', function () {
    expect(Dir::absolutePath('foo/bar/../baz'))->toBe('/foo/baz');
});

test('absolute_path resolves dot', function () {
    expect(Dir::absolutePath('foo/./bar'))->toBe('/foo/bar');
});

test('absolute_path removes multiple slashes', function () {
    expect(Dir::absolutePath('foo///bar'))->toBe('/foo/bar');
});

test('absolute_path normalizes backslashes', function () {
    expect(Dir::absolutePath('foo\\bar'))->toBe('/foo/bar');
});

test('absolute_path handles trailing slash', function () {
    expect(Dir::absolutePath('foo/bar/'))->toBe('/foo/bar');
});

test('absolute_path handles root', function () {
    expect(Dir::absolutePath('/'))->toBe('/');
});

test('absolute_path handles deep dot-dot', function () {
    expect(Dir::absolutePath('a/b/c/../../d'))->toBe('/a/d');
});

// ---------------------------------------------------------------
// create()
// ---------------------------------------------------------------

test('create creates new directory', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_create_' . uniqid();

    expect(Dir::create($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeTrue();

    Dir::rrmdir($tmp);
});

test('create creates nested directory', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_create_nested_' . uniqid() . '/a/b/c';

    expect(Dir::create($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeTrue();

    Dir::rrmdir(sys_get_temp_dir() . '/dir_test_create_nested_' . uniqid());
});

test('create returns true if directory already exists', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_create_exists_' . uniqid();
    mkdir($tmp);

    expect(Dir::create($tmp))->toBeTrue();

    rmdir($tmp);
});

// ---------------------------------------------------------------
// size()
// ---------------------------------------------------------------

test('size returns 0 for empty directory', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_size_empty_' . uniqid();
    mkdir($tmp);

    expect(Dir::size($tmp))->toBe(0);

    rmdir($tmp);
});

test('size returns correct total', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_size_total_' . uniqid();
    mkdir($tmp);
    file_put_contents($tmp . '/a.txt', 'hello'); // 5 bytes
    file_put_contents($tmp . '/b.txt', 'world!'); // 6 bytes

    expect(Dir::size($tmp))->toBe(11);

    Dir::rrmdir($tmp);
});

test('size includes subdirectories', function () {
    $tmp = sys_get_temp_dir() . '/dir_test_size_sub_' . uniqid();
    mkdir($tmp);
    mkdir($tmp . '/sub');
    file_put_contents($tmp . '/sub/file.txt', 'data'); // 4 bytes

    expect(Dir::size($tmp))->toBe(4);

    Dir::rrmdir($tmp);
});

test('size returns 0 for non-existent directory', function () {
    expect(Dir::size('/nonexistent/path/xyz'))->toBe(0);
});

// ---------------------------------------------------------------
// copy()
// ---------------------------------------------------------------

test('copy creates directory structure', function () {
    $src = sys_get_temp_dir() . '/dir_test_copy_src_' . uniqid();
    $dst = sys_get_temp_dir() . '/dir_test_copy_dst_' . uniqid();
    mkdir($src);
    mkdir($src . '/sub');
    file_put_contents($src . '/file.txt', 'hello');
    file_put_contents($src . '/sub/file2.txt', 'world');

    expect(Dir::copy($src, $dst))->toBeTrue();
    expect(is_dir($dst))->toBeTrue();
    expect(is_dir($dst . '/sub'))->toBeTrue();
    expect(file_get_contents($dst . '/file.txt'))->toBe('hello');
    expect(file_get_contents($dst . '/sub/file2.txt'))->toBe('world');

    Dir::rrmdir($src);
    Dir::rrmdir($dst);
});

test('copy returns false for non-existent source', function () {
    expect(Dir::copy('/nonexistent/path', '/tmp/dest'))->toBeFalse();
});

test('copy preserves file contents', function () {
    $src = sys_get_temp_dir() . '/dir_test_copy_content_src_' . uniqid();
    $dst = sys_get_temp_dir() . '/dir_test_copy_content_dst_' . uniqid();
    $content = str_repeat('x', 1024);
    mkdir($src);
    file_put_contents($src . '/data.bin', $content);

    Dir::copy($src, $dst);

    expect(file_get_contents($dst . '/data.bin'))->toBe($content);

    Dir::rrmdir($src);
    Dir::rrmdir($dst);
});
