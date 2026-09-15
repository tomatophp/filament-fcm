<?php

namespace TomatoPHP\FilamentFcm;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TomatoPHP\FilamentFcm\Console\FilamentFcmInstall;
use TomatoPHP\FilamentFcm\Livewire\Firebase;

class FilamentFcmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register generate command
        $this->commands([
            FilamentFcmInstall::class,
        ]);

        // Register Config file
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-fcm.php', 'filament-fcm');

        // Publish Config
        $this->publishes([
            __DIR__ . '/../config/filament-fcm.php' => config_path('filament-fcm.php'),
        ], 'filament-fcm-config');

        // Register Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish Migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'filament-fcm-migrations');

        // Register views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-fcm');

        // Publish Views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-fcm'),
        ], 'filament-fcm-views');
    }

    public function boot(): void
    {
        Livewire::component('filament-fcm', Firebase::class);

        Notification::macro('sendToFCM', function (Model $user, array $data = [], ?bool $sendToDatabase = true, ?string $type = 'fcm-web'): static {
            /** @var Notification $this */
            $payload = $this->toArray();

            $url = null;

            foreach ($this->getActions() as $action) {
                if ($action instanceof Action && filled($action->getUrl())) {
                    $url = $action->getUrl();

                    break;
                }
            }

            $title = $payload['title'] instanceof Htmlable ? $payload['title']->toHtml() : $payload['title'];
            $body = $payload['body'] instanceof Htmlable ? $payload['body']->toHtml() : $payload['body'];

            $user->notifyFCMSDK(
                message: (string) $body,
                type: $type ?? 'fcm-web',
                title: filled($title) ? (string) $title : null,
                url: $url,
                icon: is_string($payload['icon']) ? $payload['icon'] : null,
                data: [
                    ...$payload,
                    'title' => $title,
                    'body' => $body,
                    'url' => $url,
                    'data' => $data,
                ],
                sendToDatabase: $sendToDatabase ?? true,
            );

            return $this;
        });
    }
}
