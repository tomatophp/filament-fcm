<?php

namespace TomatoPHP\FilamentFcm\Livewire;

use Detection\MobileDetect;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Firebase extends Component
{
    /**
     * Store the browser / device FCM token of the signed in user, one token per provider.
     */
    #[On('fcm-token')]
    public function fcmToken(string $token): void
    {
        $user = auth()->user();

        if (! $user || blank($token)) {
            return;
        }

        $detect = new MobileDetect;
        $detect->setUserAgent((string) request()->userAgent());
        $provider = $detect->isMobile() ? 'fcm-api' : 'fcm-web';

        $userToken = $user->setFCM($provider)->userTokensFcm()->first();

        if ($userToken) {
            $userToken->provider_token = $token;
            $userToken->save();

            return;
        }

        $user->setFCM($provider)->userTokensFcm()->create([
            'provider' => $provider,
            'provider_token' => $token,
        ]);
    }

    /**
     * Show a foreground push message as a Filament notification.
     */
    #[On('fcm-notification')]
    public function fcmNotification(mixed $data = null): void
    {
        $payload = is_array($data) ? ($data['data'] ?? null) : null;

        if (! is_array($payload) || (blank($payload['title'] ?? null) && blank($payload['body'] ?? null))) {
            return;
        }

        $notification = Notification::make(filled($payload['id'] ?? null) ? (string) $payload['id'] : null)
            ->title($payload['title'] ?? null)
            ->body($payload['body'] ?? null)
            ->actions($this->actionsFrom($payload['actions'] ?? null));

        if (filled($payload['icon'] ?? null)) {
            $notification->icon($payload['icon']);
        }

        if (filled($payload['iconColor'] ?? null)) {
            $notification->iconColor($payload['iconColor']);
        }

        if (filled($payload['color'] ?? null)) {
            $notification->color($payload['color']);
        }

        if (filled($payload['status'] ?? null)) {
            $notification->status($payload['status']);
        }

        if (filled($payload['duration'] ?? null)) {
            $notification->duration(is_numeric($payload['duration']) ? (int) $payload['duration'] : $payload['duration']);
        }

        $notification->send();

        if (filter_var($payload['sendToDatabase'] ?? false, FILTER_VALIDATE_BOOL) && auth()->user()) {
            $notification->sendToDatabase(auth()->user());
        }
    }

    /**
     * Rebuild the notification actions sent in the push payload: either the serialized
     * actions of a Filament notification (a JSON list) or a single `{"url": ...}` link.
     *
     * @return array<int, Action>
     */
    protected function actionsFrom(mixed $payload): array
    {
        $decoded = is_string($payload) ? json_decode($payload, true) : $payload;

        if (! is_array($decoded) || $decoded === []) {
            return [];
        }

        if (! array_is_list($decoded)) {
            return filled($decoded['url'] ?? null)
                ? [Action::make('view')->label(trans('filament-actions::view.single.label'))->url($decoded['url'])->markAsRead()]
                : [];
        }

        $actions = [];

        foreach ($decoded as $action) {
            if (! is_array($action) || blank($action['name'] ?? null)) {
                continue;
            }

            $actions[] = Action::make($action['name'])
                ->label($action['label'] ?? $action['name'])
                ->color($action['color'] ?? null)
                ->icon($action['icon'] ?? null)
                ->url($action['url'] ?? null, (bool) ($action['shouldOpenUrlInNewTab'] ?? false))
                ->close((bool) ($action['shouldClose'] ?? false))
                ->markAsRead((bool) ($action['shouldMarkAsRead'] ?? false))
                ->markAsUnread((bool) ($action['shouldMarkAsUnread'] ?? false));
        }

        return $actions;
    }

    public function render(): View
    {
        return view('filament-fcm::firebase-base', [
            'firebaseConfig' => array_filter((array) config('filament-fcm.project'), fn (mixed $value): bool => filled($value)),
            'vapid' => (string) config('filament-fcm.vapid'),
        ]);
    }
}
