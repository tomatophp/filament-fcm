<?php

namespace TomatoPHP\FilamentFcm\Tests\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TomatoPHP\FilamentFcm\Tests\Database\Factories\UserFactory;
use TomatoPHP\FilamentFcm\Traits\InteractsWithFCM;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use InteractsWithFCM;
    use Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
