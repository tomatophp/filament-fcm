<?php

namespace TomatoPHP\FilamentFcm\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use TomatoPHP\FilamentFcm\Jobs\NotifyFCMJob;
use TomatoPHP\FilamentFcm\Models\UserToken;

trait InteractsWithFCM
{
    /**
     * The token provider used for the next push: `fcm-web` (browser) or `fcm-api` (mobile).
     */
    protected ?string $fcm = null;

    protected ?int $fcmId = null;

    public function notifyFCMSDK(
        string $message,
        string $type = 'fcm-web',
        ?string $title = null,
        ?string $url = null,
        ?string $image = null,
        ?string $icon = null,
        ?array $data = [],
        bool $sendToDatabase = true
    ): void {
        dispatch(new NotifyFCMJob([
            'user' => $this,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'image' => $image,
            'url' => $url,
            'type' => $type,
            'data' => $data,
            'sendToDatabase' => $sendToDatabase,
        ]));
    }

    public function setFcmAttribute(?string $value): void
    {
        $this->fcm = $value;
    }

    public function getFcmAttribute(): string
    {
        return $this->fcm ?? 'fcm-web';
    }

    public function setFcmIdAttribute(?int $value): void
    {
        $this->fcmId = $value;
    }

    public function getFcmIdAttribute(): mixed
    {
        return $this->fcmId ?? $this->getKey();
    }

    public function setFCM(?string $type = 'fcm-web'): static
    {
        $this->fcm = $type ?? 'fcm-web';
        $this->fcmId = $this->getKey();

        return $this;
    }

    public function userTokensFcm(): MorphOne
    {
        return $this->morphOne(UserToken::class, 'model')->where('provider', $this->fcm ?? 'fcm-web');
    }

    public function routeNotificationForFcm(): string
    {
        return (string) $this->userTokensFcm()->value('provider_token');
    }
}
