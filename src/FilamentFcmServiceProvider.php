<?php

namespace TomatoPHP\FilamentFcm;

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
        //you boot methods here
    }
}
