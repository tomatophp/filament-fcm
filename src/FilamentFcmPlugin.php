<?php

namespace TomatoPHP\FilamentFcm;

use Filament\Contracts\Plugin;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use TomatoPHP\FilamentAlerts\Resources\NotificationsLogsResource;
use TomatoPHP\FilamentAlerts\Resources\NotificationsTemplateResource;
use TomatoPHP\FilamentAlerts\Resources\UserNotificationResource;
use TomatoPHP\FilamentAlerts\Pages\EmailSettingsPage;
use TomatoPHP\FilamentAlerts\Pages\NotificationsSettingsPage;
use TomatoPHP\FilamentSettingsHub\Facades\FilamentSettingsHub;
use TomatoPHP\FilamentSettingsHub\Services\Contracts\SettingHold;


class FilamentFcmPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-fcm';
    }

    public function register(Panel $panel): void
    {
        $panel;
    }

    public function boot(Panel $panel): void
    {
        try {
            Config::set('firebase.projects.app', [
                'credentials' => env('FIREBASE_CREDENTIALS', public_path('storage/' . setting('fcm_cr'))),
                'database' => [
                    'url' => env('FIREBASE_DATABASE_URL', setting('fcm_database_url')),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error($e);
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            function (){
                return view('filament-fcm::firebase');
            },
        );
    }

    public static function make(): static
    {
        return new static();
    }
}
