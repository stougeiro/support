<?php

use STDW\Support\Dir;

// ---------------------------------------------------------------
// create() + rrmdir() roundtrip
// ---------------------------------------------------------------

test('create and rrmdir roundtrip creates and removes directory', function () {
    $tmp = sys_get_temp_dir() . '/dir_feature_roundtrip_' . uniqid();

    expect(Dir::create($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeTrue();

    file_put_contents($tmp . '/file.txt', 'test');

    expect(Dir::rrmdir($tmp))->toBeTrue();
    expect(is_dir($tmp))->toBeFalse();
});

// ---------------------------------------------------------------
// copy() + size() consistency
// ---------------------------------------------------------------

test('copy preserves total size', function () {
    $src = sys_get_temp_dir() . '/dir_feature_size_src_' . uniqid();
    $dst = sys_get_temp_dir() . '/dir_feature_size_dst_' . uniqid();
    mkdir($src);
    file_put_contents($src . '/a.txt', str_repeat('a', 500));
    file_put_contents($src . '/b.txt', str_repeat('b', 300));

    Dir::copy($src, $dst);

    expect(Dir::size($src))->toBe(Dir::size($dst));

    Dir::rrmdir($src);
    Dir::rrmdir($dst);
});

// ---------------------------------------------------------------
// absolutePath() consistency
// ---------------------------------------------------------------

test('absolute_path result is valid path format', function () {
    $result = Dir::absolutePath('some/path');

    expect(str_starts_with($result, '/'))->toBeTrue();
    expect($result)->not->toContain('..');
    expect($result)->not->toContain('/./');
});

// ---------------------------------------------------------------
// create() idempotent
// ---------------------------------------------------------------

test('create is idempotent', function () {
    $tmp = sys_get_temp_dir() . '/dir_feature_idempotent_' . uniqid();
    Dir::create($tmp);
    file_put_contents($tmp . '/file.txt', 'keep');

    expect(Dir::create($tmp))->toBeTrue();
    expect(file_get_contents($tmp . '/file.txt'))->toBe('keep');

    Dir::rrmdir($tmp);
});
