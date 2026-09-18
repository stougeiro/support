<?php

use STDW\Support\FileSystem;

// ---------------------------------------------------------------
// mkdir() + rrmdir() roundtrip
// ---------------------------------------------------------------

test('mkdir and rrmdir roundtrip creates and removes directory', function () {
    $tmp = sys_get_temp_dir() . '/fs_feature_roundtrip_' . uniqid();

    expect(FileSystem::mkdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeTrue();

    file_put_contents($tmp . '/file.txt', 'test');

    expect(FileSystem::rrmdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeFalse();
});

// ---------------------------------------------------------------
// copy() + size() consistency
// ---------------------------------------------------------------

test('copy preserves total size for directory', function () {
    $src = sys_get_temp_dir() . '/fs_feature_size_src_' . uniqid();
    $dst = sys_get_temp_dir() . '/fs_feature_size_dst_' . uniqid();
    mkdir($src);
    file_put_contents($src . '/a.txt', str_repeat('a', 500));
    file_put_contents($src . '/b.txt', str_repeat('b', 300));

    FileSystem::copy($src, $dst);

    expect(FileSystem::size($src))->toBe(FileSystem::size($dst));

    FileSystem::rrmdir($src);
    FileSystem::rrmdir($dst);
});

// ---------------------------------------------------------------
// size() + unit() integration
// ---------------------------------------------------------------

test('unit formats size result correctly', function () {
    $tmp = sys_get_temp_dir() . '/fs_feature_unit_' . uniqid();
    file_put_contents($tmp, str_repeat('x', 2048)); // 2 KB

    $size = FileSystem::size($tmp);
    $formatted = FileSystem::unit($size);

    expect($size)->toBe(2048);
    expect($formatted)->toBe('2.00 KB');

    unlink($tmp);
});

// ---------------------------------------------------------------
// rglob() recursive
// ---------------------------------------------------------------

test('rglob finds files in nested subdirectories', function () {
    $tmp = sys_get_temp_dir() . '/fs_feature_rglob_' . uniqid();
    mkdir($tmp);
    mkdir($tmp . '/a');
    mkdir($tmp . '/a/b');
    file_put_contents($tmp . '/a/file1.txt', '1');
    file_put_contents($tmp . '/a/b/file2.txt', '2');

    $result = FileSystem::rglob($tmp . '/*.txt');

    expect($result)->toHaveCount(2);

    FileSystem::rrmdir($tmp);
});
