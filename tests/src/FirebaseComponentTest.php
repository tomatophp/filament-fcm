<?php

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use TomatoPHP\FilamentFcm\Livewire\Firebase;
use TomatoPHP\FilamentFcm\Models\UserToken;
use TomatoPHP\FilamentFcm\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('stores the browser token of the signed in user', function () {
    livewire(Firebase::class)->dispatch('fcm-token', token: 'web-token-1');

    expect(UserToken::query()->where('model_id', $this->user->id)->where('provider', 'fcm-web')->value('provider_token'))
        ->toBe('web-token-1');
});

it('updates the stored browser token instead of adding a new one', function () {
    livewire(Firebase::class)->dispatch('fcm-token', token: 'web-token-1');
    livewire(Firebase::class)->dispatch('fcm-token', token: 'web-token-2');

    expect(UserToken::query()->where('model_id', $this->user->id)->where('provider', 'fcm-web')->pluck('provider_token')->all())
        ->toBe(['web-token-2']);
});

it('stores a mobile token separately from the browser token', function () {
    livewire(Firebase::class)->dispatch('fcm-token', token: 'web-token');

    request()->headers->set('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1');

    app(Firebase::class)->fcmToken('mobile-token');

    expect(UserToken::query()->where('model_id', $this->user->id)->pluck('provider_token', 'provider')->all())
        ->toBe(['fcm-web' => 'web-token', 'fcm-api' => 'mobile-token']);
});

it('shows a pushed notification with the actions of a filament notification', function () {
    livewire(Firebase::class)
        ->call('fcmNotification', ['data' => [
            'id' => 'push-1',
            'title' => 'Order shipped',
            'body' => 'Your order is on the way',
            'actions' => json_encode([
                ['name' => 'open', 'label' => 'Open order', 'url' => 'https://tomatophp.com/orders/1', 'shouldMarkAsRead' => true],
            ]),
            'sendToDatabase' => '0',
        ]])
        ->assertHasNoErrors();

    Notification::assertNotified(
        Notification::make('push-1')
            ->title('Order shipped')
            ->body('Your order is on the way')
            ->actions([
                Action::make('open')
                    ->label('Open order')
                    ->color(null)
                    ->icon(null)
                    ->url('https://tomatophp.com/orders/1', false)
                    ->close(false)
                    ->markAsRead(true)
                    ->markAsUnread(false),
            ])
    );
});

it('shows a pushed notification that only has a title and a body', function () {
    livewire(Firebase::class)
        ->call('fcmNotification', ['data' => ['title' => 'Hello', 'body' => 'World']])
        ->assertHasNoErrors();

    Notification::assertNotified('Hello');
});

it('ignores an empty push payload', function () {
    livewire(Firebase::class)
        ->call('fcmNotification', [])
        ->assertHasNoErrors();

    Notification::assertNotNotified();
});
