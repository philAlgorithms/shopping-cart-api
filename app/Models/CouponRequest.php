<?php

namespace App\Models;

use App\Models\Order\Coupon;
use App\Models\Users\Buyer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class CouponRequest extends Model
{
    use HasFactory;

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function getHasCouponAttribute(): bool
    {
        return ! is_null($this->coupon);
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->has_coupon;
    }

    public function approve(Coupon $coupon)
    {
        if($this->is_approved)
        {
            throw ValidationException::withMessages([
                'coupon' => 'This request has already been approved.'
            ]);
        }
        if($this->coupon->has_request)
        {
            throw ValidationException::withMessages([
                'coupon' => 'The selected coupon is already attached to another coupon request.'
            ]);
        }

        return $this->update([
            'coupon_id' => $coupon->id,
            'approved_at' => now()
        ]);
    }
}
