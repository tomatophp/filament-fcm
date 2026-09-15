<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->publicPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'filament-fcm-' . uniqid();
    File::ensureDirectoryExists($this->publicPath);
    app()->usePublicPath($this->publicPath);
});

afterEach(fn () => File::deleteDirectory($this->publicPath));

it('generates the firebase messaging service worker', function () {
    config([
        'filament-fcm.project.apiKey' => 'test-api-key',
        'filament-fcm.project.projectId' => 'tomato-test',
        'filament-fcm.alert.sound' => null,
    ]);

    $this->artisan('filament-fcm:install')->assertSuccessful();

    $worker = File::get($this->publicPath . '/firebase-messaging-sw.js');

    expect($worker)
        ->toContain('apiKey: "test-api-key"')
        ->toContain('projectId: "tomato-test"')
        ->toContain('onBackgroundMessage')
        ->not->toContain('{{ ');
});
