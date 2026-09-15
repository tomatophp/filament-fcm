<?php

namespace TomatoPHP\FilamentFcm\Console;

use Illuminate\Console\Command;
use TomatoPHP\ConsoleHelpers\Traits\HandleStub;

class FilamentFcmInstall extends Command
{
    use HandleStub;

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
    protected $description = 'Generate the Firebase Messaging service worker (public/firebase-messaging-sw.js) for Filament FCM.';

    public function handle(): int
    {
        $this->info('Install FCM Worker');

        $sound = config('filament-fcm.alert.sound');

        $this->generateStubs(
            __DIR__ . '/../../stubs/firebase.stub',
            public_path('firebase-messaging-sw.js'),
            [
                'apiKey' => (string) config('filament-fcm.project.apiKey'),
                'authDomain' => (string) config('filament-fcm.project.authDomain'),
                'databaseURL' => (string) config('filament-fcm.project.databaseURL'),
                'projectId' => (string) config('filament-fcm.project.projectId'),
                'storageBucket' => (string) config('filament-fcm.project.storageBucket'),
                'messagingSenderId' => (string) config('filament-fcm.project.messagingSenderId'),
                'appId' => (string) config('filament-fcm.project.appId'),
                'measurementId' => (string) config('filament-fcm.project.measurementId'),
                'sound' => filled($sound) ? 'new Audio(' . json_encode($sound) . ').play().catch(() => {});' : '',
            ]
        );

        $this->info('Filament FCM service worker generated at ' . public_path('firebase-messaging-sw.js'));

        return self::SUCCESS;
    }
}
