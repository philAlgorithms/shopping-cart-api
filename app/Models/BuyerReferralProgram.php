<?php

namespace App\Models;

use App\Mail\Referral\Buyer\Program\BuyerReferralProgramRequest;
use App\Mail\Referral\Buyer\Program\BuyerReferralProgramRequestNotice;
use App\Models\Users\{Admin, Buyer};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

use function Illuminate\Events\queueable;

class BuyerReferralProgram extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(queueable(function (BuyerReferralProgram $program) {
            // Send mail to buyer
            Mail::to($program->buyer->email)->queue(new BuyerReferralProgramRequest($program));

            // Notify all admins of this request
            foreach(Admin::all() as $admin)
            {
                Mail::to($admin->email)->queue(new BuyerReferralProgramRequestNotice($program));
            }
        }));
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(BuyerReferral::class, 'buyer_referral_program_id');
    }

    public function activator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'activator_id');
    }

    public function deactivator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'deactivator_id');
    }

    public function getIsActivatedAttribute(): bool
    {
        return ! is_null($this->activated_at) && (strtotime($this->activated_at) > strtotime($this->deactivated_at));
    }

    public function getIsActivatableableAttribute(): bool
    {
        return !$this->is_activated;
    }

    public function activate(Admin $admin)
    {
        if($this->is_activated)
        {
            throw ValidationException::withMessages([
                'program' => 'This referral program has already been activated.'
            ]);
        }

        return $this->update([
            'activated_at' => now(),
            'activator_id' => $admin->id
        ]);
    }

    public function getIsDeactivatedAttribute(): bool
    {
        return is_null($this->activated_at) || (! is_null($this->deactivated_at) && (strtotime($this->deactivated_at) > strtotime($this->activated_at)));
    }

    public function getIsDeactivatableableAttribute(): bool
    {
        return !$this->is_deactivated;
    }

    public function deactivate(Admin $admin)
    {
        if($this->is_deactivated)
        {
            throw ValidationException::withMessages([
                'program' => 'This referral program has already been deactivated.'
            ]);
        }

        return $this->update([
            'deactivated_at' => now(),
            'deactivator_id' => $admin->id
        ]);
    }

    public function hasBeenReferred(Buyer $buyer): bool
    {
        return $this->referrals()->where('referee_id', $buyer->id)->count() > 0;
    }

    public function addReferral(Buyer $buyer)
    {
        if($buyer->id == $this->buyer->id)
        {
            throw ValidationException::withMessages([
                'program' => 'Canot refer oneself.'
            ]);
        }
        else if($this->is_deactivated)
        {
            throw ValidationException::withMessages([
                'program' => 'This referral program has been deactivated.'
            ]);
        }
        else if($this->hasBeenReferred($buyer))
        {
            throw ValidationException::withMessages([
                'program' => 'The selected buyer has been referred with this code already.'
            ]);
        }

        return $this->referrals()->create([
            'referee_id' => $buyer->id,
            'reward' => 1000 // Consult with the team later concerning this value
        ]);
    }
}
