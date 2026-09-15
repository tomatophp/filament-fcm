![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-fcm/master/arts/3x1io-tomato-fcm.jpg)

# Filament Firebase Integration

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-fcm/version.svg)](https://packagist.org/packages/tomatophp/filament-fcm)
[![License](https://poser.pugx.org/tomatophp/filament-fcm/license.svg)](https://packagist.org/packages/tomatophp/filament-fcm)
[![Downloads](https://poser.pugx.org/tomatophp/filament-fcm/d/total.svg)](https://packagist.org/packages/tomatophp/filament-fcm)

Firebase Cloud Messaging integration to Native FilamentPHP Notification Package

## Version Compatibility

| Filament | Laravel   | PHP  | filament-fcm |
|----------|-----------|------|--------------|
| v5       | 12.x / 13.x | 8.2+ | 5.x        |
| v3       | 10.x / 11.x | 8.1+ | 1.x (`v3` branch) |

> Using [filament-alerts](https://github.com/tomatophp/filament-alerts)? Use [filament-fcm-driver](https://github.com/tomatophp/filament-fcm-driver) instead: it registers FCM as an alerts driver with a settings page. This package is the standalone integration for the native Filament notifications and has no dependency on filament-alerts or the settings hub.

## Installation

```bash
composer require tomatophp/filament-fcm
```

after install your package you need to update this keys in your `.env` file

```dotenv
# Firebase Project (web SDK, used by the browser)
FIREBASE_API_KEY=
FIREBASE_AUTH_DOMAIN=
FIREBASE_DATABASE_URL=
FIREBASE_PROJECT_ID=
FIREBASE_STORAGE_BUCKET=
FIREBASE_MESSAGING_SENDER_ID=
FIREBASE_APP_ID=
FIREBASE_MEASUREMENT_ID=

# Firebase Admin SDK service account JSON (used to send the pushes, keep it outside public/)
FIREBASE_CREDENTIALS=/absolute/path/to/firebase-service-account.json

# Firebase Cloud Messaging web push certificate (VAPID key)
FIREBASE_VAPID=

# Firebase Alert Sound (optional)
FCM_ALERT_SOUND=
```

The Firebase JS SDK is only added to the panel when `FIREBASE_API_KEY`, `FIREBASE_PROJECT_ID`, `FIREBASE_MESSAGING_SENDER_ID`, `FIREBASE_APP_ID` and `FIREBASE_VAPID` are set.

after update clear config

```bash
php artisan config:clear
```

then run the migrations (creates the `user_has_notifications` table that stores the FCM tokens)

```bash
php artisan migrate
```

and generate the service worker `public/firebase-messaging-sw.js` (run it again after changing the Firebase keys)

```bash
php artisan filament-fcm:install
```

register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\FilamentFcm\FilamentFcmPlugin::make())
```

and add the trait to your user model

```php
use TomatoPHP\FilamentFcm\Traits\InteractsWithFCM;

class User extends Authenticatable
{
    use InteractsWithFCM;
    use Notifiable;
}
```

The plugin adds no pages: it renders a small Livewire component at the end of the panel body that asks the browser for notification permission, stores the FCM token of the signed in user (`fcm-web` for browsers, `fcm-api` for mobile devices) and shows foreground pushes as Filament notifications.

## Usage

you can use the filament native notification and we add some macro for you

```php
use Filament\Actions\Action;
use Filament\Notifications\Notification;

Notification::make('send')
    ->title('Test Notifications')
    ->body('This is a test notification')
    ->icon('heroicon-o-bell')
    ->color('success')
    ->actions([
        Action::make('view')
            ->label('View')
            ->url('https://google.com')
            ->markAsRead(),
    ])
    ->sendToFCM(
        user: auth()->user(),
        data: [
            'key' => 'value'
        ],
        sendToDatabase: false,
        type: 'fcm-web' // or fcm-api
    );
```

or you can send it directly from the user model

```php
$user->notifyFCMSDK(
    message: 'This is a test notification',
    type: 'fcm-web', // or fcm-api
    title: 'Test Notifications',
    url: 'https://google.com',
    image: null,
    icon: 'heroicon-o-bell',
    data: [
        'url' => 'https://google.com',
        'actions' => [],
        'data' => ['key' => 'value'],
    ],
    sendToDatabase: false
);
```

Pushes are sent by the queued `TomatoPHP\FilamentFcm\Jobs\NotifyFCMJob` through the `laravel-notification-channels/fcm` channel, so run a queue worker in production.

## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-fcm-config"
```

you can publish views file by use this command

```bash
php artisan vendor:publish --tag="filament-fcm-views"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="filament-fcm-migrations"
```

## Testing

```bash
composer test
```

## Other Filament Packages

Checkout our [Awesome TomatoPHP](https://github.com/tomatophp/awesome)
