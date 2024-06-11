![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-fcm/master/arts/3x1io-tomato-fcm.jpg)

# Filament Firebase Integration

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-fcm/version.svg)](https://packagist.org/packages/tomatophp/filament-fcm)
[![PHP Version Require](http://poser.pugx.org/tomatophp/filament-fcm/require/php)](https://packagist.org/packages/tomatophp/filament-fcm)
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

## Support

you can join our discord server to get support [TomatoPHP](https://discord.gg/Xqmt35Uh)

## Docs

you can check docs of this package on [Docs](https://docs.tomatophp.com/filament/filament-fcm)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](https://wa.me/+201207860084)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
