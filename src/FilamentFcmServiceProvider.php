<?php

namespace TomatoPHP\FilamentFcm;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TomatoPHP\FilamentFcm\Livewire\Firebase;


class FilamentFcmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register generate command
        $this->commands([
           \TomatoPHP\FilamentFcm\Console\FilamentFcmInstall::class,
        ]);

        //Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/filament-fcm.php', 'filament-fcm');

        //Publish Config
        $this->publishes([
           __DIR__.'/../config/filament-fcm.php' => config_path('filament-fcm.php'),
        ], 'filament-fcm-config');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        //Publish Migrations
        $this->publishes([
           __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filament-fcm-migrations');

        //Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-fcm');

        //Publish Views
        $this->publishes([
           __DIR__.'/../resources/views' => resource_path('views/vendor/filament-fcm'),
        ], 'filament-fcm-views');

        Livewire::component(Firebase::class);
    }

    public function boot(): void
    {

        Notification::macro('sendToFCM', function (Model $user, array $data=[], ?bool $sendToDatabase=true): static
        {
            /** @var Notification $this */
            $user->notifyFCMSDK(
                title: $this->title,
                message: $this->body,
                type: 'fcm-web',
                url: count($this->actions)? $this->actions[0]->getUrl()  : null,
                icon: $this->icon,
                data: [
                    'url' => count($this->actions)? $this->actions[0]->getUrl()  : null,
                    'id' => $this->getId(),
                    'actions' => array_map(fn (Action | ActionGroup $action): array => $action->toArray(), $this->getActions()),
                    'body' => $this->getBody(),
                    'color' => $this->getColor(),
                    'duration' => $this->getDuration(),
                    'icon' => $this->getIcon(),
                    'iconColor' => $this->getIconColor(),
                    'status' => $this->getStatus(),
                    'title' => $this->getTitle(),
                    'view' => $this->getView(),
                    'viewData' => $this->getViewData(),
                    'data'=> $data
                ],
                sendToDatabase: $sendToDatabase
            );

            return $this;
        });
    }
}
