<?php

namespace TomatoPHP\FilamentFcm\Console;

use Illuminate\Console\Command;
use TomatoPHP\ConsoleHelpers\Traits\RunCommand;

class FilamentFcmInstall extends Command
{
    use RunCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'filament-fcm:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate FCM Worker for Filament FCM.';

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Install FCM Worker');
        $this->generateStubs(
            __DIR__ . '/../../stubs/firebase.stub',
            public_path('firebase-messaging-sw.js'),
            [
                'apiKey' => config('filament-fcm.project.apiKey'),
                'authDomain' => config('filament-fcm.project.authDomain'),
                'databaseURL' => config('filament-fcm.project.databaseURL'),
                'projectId' => config('filament-fcm.project.projectId'),
                'storageBucket' => config('filament-fcm.project.storageBucket'),
                'messagingSenderId' => config('filament-fcm.project.messagingSenderId'),
                'appId' => config('filament-fcm.project.appId'),
                'measurementId' => config('filament-fcm.project.measurementId'),
                'sound' => config('filament-fcm.alert.sound') ? "var audio = new Audio('".config('filament-fcm.alert.sound')."');\n audio.play();": null
            ]
        );
        $this->info('Filament Alerts FCM installed successfully.');
    }
}
