<?php

namespace TomatoPHP\FilamentFcm\Notifications;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class FCMNotificationService extends Notification
{
    public function __construct(
        public string $message,
        public ?string $type = 'fcm-web',
        public ?string $title = null,
        public ?string $icon = null,
        public ?string $image = null,
        public ?string $url = null,
        public ?array $data = [],
        public ?bool $sendToDatabase = true,
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(mixed $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toFcm(mixed $notifiable): FcmMessage
    {
        $data = $this->data ?? [];

        return new FcmMessage(
            data: array_map(static::stringify(...), [
                'id' => $data['id'] ?? null,
                'actions' => $data['actions'] ?? [],
                'body' => $data['body'] ?? $this->message,
                'color' => $data['color'] ?? null,
                'duration' => $data['duration'] ?? null,
                'icon' => $data['icon'] ?? $this->icon,
                'iconColor' => $data['iconColor'] ?? null,
                'image' => $this->image,
                'status' => $data['status'] ?? null,
                'title' => $data['title'] ?? $this->title,
                'url' => $data['url'] ?? $this->url,
                'view' => $data['view'] ?? null,
                'viewData' => $data['viewData'] ?? [],
                'data' => $data['data'] ?? [],
                'sendToDatabase' => (bool) $this->sendToDatabase,
            ]),
            custom: [
                'android' => [
                    'notification' => [
                        'color' => '#0A0A0A',
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
                'apns' => [
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
            ],
            notification: new FcmNotification(
                title: $this->title,
                body: $this->message,
                image: $this->image,
            ),
        );
    }

    /**
     * FCM data payload values must be strings.
     */
    public static function stringify(mixed $value): string
    {
        return match (true) {
            $value === null => '',
            is_bool($value) => $value ? '1' : '0',
            $value instanceof BackedEnum => (string) $value->value,
            $value instanceof Htmlable => $value->toHtml(),
            is_scalar($value) => (string) $value,
            default => (string) json_encode($value),
        };
    }
}
