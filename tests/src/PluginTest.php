<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use TomatoPHP\FilamentFcm\FilamentFcmPlugin;
use TomatoPHP\FilamentFcm\Livewire\Firebase;

function configureFirebaseWebProject(): void
{
    config([
        'filament-fcm.project.apiKey' => 'test-api-key',
        'filament-fcm.project.projectId' => 'tomato-test',
        'filament-fcm.project.messagingSenderId' => '123456',
        'filament-fcm.project.appId' => '1:123456:web:abc',
        'filament-fcm.vapid' => 'test-vapid-key',
    ]);
}

it('boots the service provider', function () {
    expect(config('filament-fcm.project'))->toBeArray()
        ->and(config()->has('filament-fcm.credentials'))->toBeTrue()
        ->and(view()->exists('filament-fcm::firebase'))->toBeTrue()
        ->and(view()->exists('filament-fcm::firebase-base'))->toBeTrue()
        ->and(Artisan::all())->toHaveKey('filament-fcm:install')
        ->and(app('livewire.factory')->resolveComponentClass('filament-fcm'))->toBe(Firebase::class);
});

it('registers the plugin on the panel', function () {
    expect(Filament::getPanel('admin')->getPlugin('filament-fcm'))->toBeInstanceOf(FilamentFcmPlugin::class);
});

it('creates the user_has_notifications table', function () {
    expect(Schema::hasTable('user_has_notifications'))->toBeTrue()
        ->and(Schema::hasColumns('user_has_notifications', ['model_type', 'model_id', 'provider', 'provider_token']))->toBeTrue();
});

it('boots the panel without the settings hub and without credentials', function () {
    config(['filament-fcm.credentials' => null, 'firebase.projects.app.credentials' => null]);

    Filament::getPanel('admin')->getPlugin('filament-fcm')->boot(Filament::getPanel('admin'));

    expect(config('firebase.projects.app.credentials'))->toBeNull();
});

it('configures the firebase admin credentials from the package config', function () {
    config([
        'filament-fcm.credentials' => '/secure/firebase.json',
        'filament-fcm.project.databaseURL' => 'https://tomato-test.firebaseio.com',
    ]);

    Filament::getPanel('admin')->getPlugin('filament-fcm')->boot(Filament::getPanel('admin'));

    expect(config('firebase.projects.app.credentials'))->toBe('/secure/firebase.json')
        ->and(config('firebase.projects.app.database.url'))->toBe('https://tomato-test.firebaseio.com');
});

it('injects the firebase scripts when the web project is configured', function () {
    configureFirebaseWebProject();

    $this->get('/admin/login')
        ->assertSuccessful()
        ->assertSee('firebasejs/10.12.2/firebase-messaging.js', false)
        ->assertSee('test-vapid-key', false)
        ->assertSee('/firebase-messaging-sw.js', false);

    expect(FilamentFcmPlugin::isWebPushConfigured())->toBeTrue();
});

it('renders nothing when the web project is not configured', function () {
    config([
        'filament-fcm.project.apiKey' => null,
        'filament-fcm.project.projectId' => null,
        'filament-fcm.project.messagingSenderId' => null,
        'filament-fcm.project.appId' => null,
        'filament-fcm.vapid' => null,
    ]);

    $this->get('/admin/login')
        ->assertSuccessful()
        ->assertDontSee('gstatic.com', false)
        ->assertDontSee('firebase-messaging-sw.js', false);

    expect(FilamentFcmPlugin::isWebPushConfigured())->toBeFalse();
});
