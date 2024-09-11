![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-fcm/master/arts/3x1io-tomato-fcm.jpg)

# Filament Firebase Integration

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-fcm/version.svg)](https://packagist.org/packages/tomatophp/filament-fcm)
[![License](https://poser.pugx.org/tomatophp/filament-fcm/license.svg)](https://packagist.org/packages/tomatophp/filament-fcm)
[![Downloads](https://poser.pugx.org/tomatophp/filament-fcm/d/total.svg)](https://packagist.org/packages/tomatophp/filament-fcm)

Firebase Cloud Messaging integration to Native FilamentPHP Notification Package

## Installation

```bash
composer require tomatophp/filament-fcm
```

after install your package you need to update this keys in your `.env` file

```dotenv
# Firebase Project
FIREBASE_API_KEY=
FIREBASE_AUTH_DOMAIN=
FIREBASE_DATABASE_URL=
FIREBASE_PROJECT_ID=
FIREBASE_STORAGE_BUCKET=
FIREBASE_MESSAGING_SENDER_ID=
FIREBASE_APP_ID=
FIREBASE_MEASUREMENT_ID=

# Firebase Admin SDK
FIREBASE_CREDENTIALS=

# Firebase Cloud Messaging
FIREBASE_VAPID=

# Firebase Alert Sound
FCM_ALERT_SOUND=
```

after update clear config

```bash
php artisan config:clear
```

then please run this command

```bash
php artisan filament-fcm:install
```

if you are not using this package as a plugin please register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\FilamentFcm\FilamentFcmPlugin::make()
)
```

## Usage

you can use the filament native notification and we add some macro for you

```php
use Filament\Notifications\Notification;

Notification::make('send')
    ->title('Test Notifications')
    ->body('This is a test notification')
    ->icon('heroicon-o-bell')
    ->color('success')
    ->actions([
    \Filament\Notifications\Actions\Action::make('view')
        ->label('View')
        ->url('https://google.com')
        ->markAsRead()
    ])
    ->sendToFCM(
        user: auth()->user(),
        data: [
            'key' => 'value'
        ],
        sendToDatabase: false,
        type: 'fcm-web' // or fcm-api
    )
```

or you can send it directly from the user model

```php

$user->notifyFCMSDK(
    message: $this->message,
    type: $this->provider,
    title: $this->title,
    url: $this->url,
    image: $this->image,
    icon: $this->icon,
    data: [
        'url' => $this->url,
        'id' => $this->model_id,
        'actions' => [],
        'body' => $this->message,
        'color' => null,
        'duration' => null,
        'icon' => $this->icon,
        'iconColor' => null,
        'status' => null,
        'title' => $this->title,
        'view' => null,
        'viewData' => null,
        'data'=> $this->data
    ],
    sendToDatabase: false
);

```
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

## Other Filament Packages

Checkout our [Awesome TomatoPHP](https://github.com/tomatophp/awesome)
