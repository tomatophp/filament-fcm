<?php

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use TomatoPHP\FilamentFcm\Jobs\NotifyFCMJob;
use TomatoPHP\FilamentFcm\Models\UserToken;
use TomatoPHP\FilamentFcm\Notifications\FCMNotificationService;
use TomatoPHP\FilamentFcm\Tests\Models\User;

function storeToken(User $user, string $provider, string $token): void
{
    UserToken::query()->create([
        'model_type' => $user::class,
        'model_id' => $user->id,
        'provider' => $provider,
        'provider_token' => $token,
    ]);
}

it('dispatches the fcm job from a native filament notification', function () {
    Bus::fake();
    $user = User::factory()->create();

    Notification::make('order-1')
        ->title('Order shipped')
        ->body('Your order is on the way')
        ->icon(Heroicon::OutlinedBell)
        ->color('success')
        ->actions([
            Action::make('view')->label('View')->url('https://tomatophp.com/orders/1')->markAsRead(),
        ])
        ->sendToFCM($user, ['order' => 1], false, 'fcm-api');

    Bus::assertDispatched(NotifyFCMJob::class, function (NotifyFCMJob $job) use ($user): bool {
        return $job->user->is($user)
            && $job->title === 'Order shipped'
            && $job->message === 'Your order is on the way'
            && $job->type === 'fcm-api'
            && $job->url === 'https://tomatophp.com/orders/1'
            && is_string($job->icon)
            && $job->sendToDatabase === false
            && $job->data['id'] === 'order-1'
            && $job->data['data'] === ['order' => 1]
            && $job->data['actions'][0]['name'] === 'view'
            && $job->data['actions'][0]['url'] === 'https://tomatophp.com/orders/1';
    });
});

it('sends the fcm notification to the user when the job runs', function () {
    NotificationFacade::fake();
    $user = User::factory()->create();
    storeToken($user, 'fcm-web', 'web-token');

    (new NotifyFCMJob(['user' => $user, 'title' => 'Hi', 'message' => 'Body', 'type' => 'fcm-web', 'data' => []]))->handle();

    NotificationFacade::assertSentTo($user, FCMNotificationService::class, function (FCMNotificationService $notification, array $channels): bool {
        return $channels === [FcmChannel::class] && $notification->title === 'Hi' && $notification->message === 'Body';
    });
});

it('routes fcm notifications to the stored token of the selected provider', function () {
    $user = User::factory()->create();
    storeToken($user, 'fcm-web', 'web-token');
    storeToken($user, 'fcm-api', 'mobile-token');

    expect($user->routeNotificationForFcm())->toBe('web-token')
        ->and($user->setFCM('fcm-api')->routeNotificationForFcm())->toBe('mobile-token')
        ->and($user->setFCM('fcm-web')->routeNotificationForFcm())->toBe('web-token')
        ->and(User::factory()->create()->routeNotificationForFcm())->toBe('');
});

it('builds a valid fcm message', function () {
    $service = new FCMNotificationService(
        message: 'Your order is on the way',
        type: 'fcm-web',
        title: 'Order shipped',
        image: 'https://tomatophp.com/logo.png',
        url: 'https://tomatophp.com/orders/1',
        data: [
            'id' => 'order-1',
            'actions' => [['name' => 'view', 'url' => 'https://tomatophp.com/orders/1']],
            'duration' => 6000,
            'data' => ['order' => 1],
        ],
        sendToDatabase: true,
    );

    $message = $service->toFcm(User::factory()->make());

    expect($service->via(null))->toBe([FcmChannel::class])
        ->and($message)->toBeInstanceOf(FcmMessage::class)
        ->and($message->notification->title)->toBe('Order shipped')
        ->and($message->notification->body)->toBe('Your order is on the way')
        ->and($message->data)->each->toBeString()
        ->and($message->data['id'])->toBe('order-1')
        ->and($message->data['url'])->toBe('https://tomatophp.com/orders/1')
        ->and($message->data['duration'])->toBe('6000')
        ->and($message->data['sendToDatabase'])->toBe('1')
        ->and(json_decode($message->data['actions'], true))->toBe([['name' => 'view', 'url' => 'https://tomatophp.com/orders/1']])
        ->and(json_decode($message->data['data'], true))->toBe(['order' => 1]);
});

it('builds an fcm message without notification data', function () {
    $message = (new FCMNotificationService('Body', data: []))->toFcm(null);

    expect($message->data)->each->toBeString()
        ->and($message->data['body'])->toBe('Body');
});
