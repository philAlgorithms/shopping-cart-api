<?php

namespace App\Models;

use App\Mail\Referral\Buyer\NewBuyerReferral;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

use function Illuminate\Events\queueable;

class BuyerReferral extends Model
{
    use HasFactory;
    protected static function booted(): void
    {
        static::created(queueable(function (BuyerReferral $referral) {
            // Send mail to referrer
            Mail::to($referral->program->buyer->email)->queue(new NewBuyerReferral());
            // Send mail to referee?
        }));
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(BuyerReferralProgram::class, 'buyer_referral_program_id');
    }

    public function getIsActiveAttribute(): bool
    {
        return ! is_null($this->activated_at);
    }
}
