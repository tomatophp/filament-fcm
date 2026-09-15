# Changelog

## v5.0.0

- Support Filament v5, Livewire 4 and Laravel 12 / 13 (PHP 8.2+).
- Replace the Laravel 10/11-only `tomatophp/fcm-notifications` with upstream `laravel-notification-channels/fcm` (^5.0|^6.0, same `NotificationChannels\Fcm` namespace).
- Firebase Admin credentials now come from `FIREBASE_CREDENTIALS` (`filament-fcm.credentials`); the plugin no longer calls the settings-hub `setting()` helper, which crashed panels without that package.
- The panel render hook only injects the Firebase JS SDK when `FIREBASE_API_KEY`, `FIREBASE_PROJECT_ID`, `FIREBASE_MESSAGING_SENDER_ID`, `FIREBASE_APP_ID` and `FIREBASE_VAPID` are set.
- Foreground push messages are rebuilt with `Filament\Actions\Action` and no longer crash when payload keys are missing.
- `Notification::sendToFCM()` works with enum icons (e.g. `Heroicon::OutlinedBell`) and uses the notification getters.
- FCM data payload values are always strings, and the message includes the notification `url` and `image`.
- `InteractsWithFCM`: the provider defaults to `fcm-web`, `routeNotificationForFcm()` always reads the token of the selected provider.
- `filament-fcm:install` no longer fails when optional Firebase keys or the alert sound are empty; the service worker opens the notification link on click.
- Added a Pest test suite.
