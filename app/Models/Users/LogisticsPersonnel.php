<?php

namespace App\Models\Users;

use App\Models\{User};
use App\Models\Location\Town;
use App\Notifications\Registration\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\{Builder, Collection};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough, HasOne};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class LogisticsPersonnel extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasApiTokens, HasRoles, Notifiable;

    protected $guard_name = 'buyer';

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

    public function baseTown(): BelongsTo
    {
        return $this->belongsTo(Town::class, 'base_town_id');
    }

    public function destinationTown(): BelongsTo
    {
        return $this->belongsTo(Town::class, 'destination_town_id');
    }
}
