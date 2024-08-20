<?php

namespace App\Models\Users;

use App\Models\User;
use App\Notifications\Registration\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasApiTokens, HasRoles, Notifiable;

    protected $guard_name = 'admin';
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sendEmailVerificationNotification()
    {
        // We override the default notification and will use our own
        $this->notify(new VerifyEmailNotification());
    }
}
