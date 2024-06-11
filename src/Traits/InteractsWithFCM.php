<?php

namespace TomatoPHP\FilamentAlerts\Traits;

use TomatoPHP\FilamentFcm\Jobs\NotifyFCMJob;
use TomatoPHP\FilamentFcm\Models\UserToken;

trait InteractsWithFCM
{

    protected ?string $fcm;
    protected ?int $fcmId;

    public function notifyFCMSDK(
        string $message,
        string $type='web',
        ?string $title=null,
        ?string $url=null,
        ?string $image=null,
        ?string $icon=null,
        ?array $data=[]
    )
    {
        dispatch(new NotifyFCMJob([
            'user' => $this,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'image' => $image,
            'url' => $url,
            'type' => $type,
            'data' => $data,
        ]));
    }

    public function initializeUseNotifications()
    {
        $this->appends[] = 'fcm';
        $this->appends[] = 'fcmID';
    }

    public function setFcmAttribute($value)
    {
        $this->fcm = $value;
    }

    public function getFcmAttribute()
    {
        return 'fcm-web';
    }

    public function setFcmIdAttribute($value)
    {
        $this->fcmId = $value;
    }

    public function getFcmIdAttribute()
    {
        return $this->id;
    }

    public function setFCM(?string $type='fcm-web'): static
    {
        $this->fcm = $type;
        $this->fcmId = $this->id;
        return $this;
    }

    public function userTokensFcm()
    {
        return $this->morphOne(UserToken::class, 'model')->where('provider', $this->fcm);
    }

    public function routeNotificationForFcm()
    {
        return $this->userTokensFcm ? $this->userTokensFcm->provider_token : '';
    }
}
