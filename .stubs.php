<?php

namespace Filament\Notifications;
use Illuminate\Database\Eloquent\Model;

{
    /*
     * @method static static sendToFCM(Model $user, array $data=[])
     */
    class Notification
    {
        public function sendToFCM(Model $user, array $data=[]): static {}
    }
}
