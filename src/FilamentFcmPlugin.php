<?php

namespace TomatoPHP\FilamentFcm;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;

class FilamentFcmPlugin implements Plugin
{
    /**
     * The web SDK keys that must be set before the Firebase JS is injected in the panel.
     *
     * @var array<int, string>
     */
    public const REQUIRED_WEB_CONFIG = [
        'filament-fcm.project.apiKey',
        'filament-fcm.project.projectId',
        'filament-fcm.project.messagingSenderId',
        'filament-fcm.project.appId',
        'filament-fcm.vapid',
    ];

    public function getId(): string
    {
        return 'filament-fcm';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void
    {
        $credentials = config('filament-fcm.credentials');

        if (filled($credentials)) {
            Config::set('firebase.projects.app.credentials', $credentials);

            $databaseUrl = config('filament-fcm.project.databaseURL');

            if (filled($databaseUrl)) {
                Config::set('firebase.projects.app.database.url', $databaseUrl);
            }
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): View | string => static::isWebPushConfigured() ? view('filament-fcm::firebase') : '',
        );
    }

    /**
     * Web push only works when the Firebase web project and the VAPID key are configured.
     */
    public static function isWebPushConfigured(): bool
    {
        foreach (self::REQUIRED_WEB_CONFIG as $key) {
            if (blank(config($key))) {
                return false;
            }
        }

        return true;
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
