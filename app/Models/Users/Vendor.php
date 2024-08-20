<?php

namespace App\Models\Users;

use App\Models\Stores\Store;
use App\Models\{Cart, User};
use App\Notifications\Registration\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Vendor extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasApiTokens, HasRoles, Notifiable;

    protected $guard_name = 'vendor';

    // Method to send email verification
    public function sendEmailVerificationNotification()
    {
        // We override the default notification and will use our own
        $this->notify(new VerifyEmailNotification());
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    public function getHasStoreAttribute(): bool
    {
        return !is_null($this->store);
    }
}
