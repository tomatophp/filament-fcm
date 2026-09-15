<?php

namespace TomatoPHP\FilamentFcm\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TomatoPHP\FilamentFcm\Notifications\FCMNotificationService;

class NotifyFCMJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ?Model $user;

    public ?string $title;

    public ?string $message;

    public ?string $image;

    public ?string $icon;

    public ?string $url;

    public ?string $type;

    public ?array $data;

    public ?bool $sendToDatabase = true;

    /**
     * @param  array{user: Model, title?: ?string, message?: ?string, icon?: ?string, url?: ?string, image?: ?string, type?: ?string, data?: ?array, sendToDatabase?: ?bool}  $arrgs
     */
    public function __construct(array $arrgs)
    {
        $this->user = $arrgs['user'];
        $this->title = $arrgs['title'] ?? null;
        $this->message = $arrgs['message'] ?? '';
        $this->icon = $arrgs['icon'] ?? null;
        $this->url = $arrgs['url'] ?? null;
        $this->image = $arrgs['image'] ?? null;
        $this->type = $arrgs['type'] ?? 'fcm-web';
        $this->data = $arrgs['data'] ?? [];
        $this->sendToDatabase = $arrgs['sendToDatabase'] ?? true;
    }

    public function handle(): void
    {
        $this->user->setFCM($this->type)->notify(new FCMNotificationService(
            (string) $this->message,
            $this->type,
            $this->title,
            $this->icon,
            $this->image,
            $this->url,
            $this->data,
            $this->sendToDatabase,
        ));
    }
}
