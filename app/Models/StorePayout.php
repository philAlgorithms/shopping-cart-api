<?php

namespace App\Models;

use App\Models\Stores\Store;
use App\Models\Users\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class StorePayout extends Model
{
    use HasFactory, SoftDeletes;

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approver_id');
    }

    public function decliner(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'decliner_id');
    }

    public function getIsApprovedAttribute(): bool
    {
        return ! is_null($this->approved_at) && is_null($this->declined_at);
    }

    public function getIsApprovableAttribute(): bool
    {
        return !$this->is_approved && !$this->is_declined;
    }

    public function approve(Admin $admin)
    {
        if($this->is_approved)
        {
            throw ValidationException::withMessages([
                'payout' => 'This payout has already been approved.'
            ]);
        }

        return $this->update([
            'approved_at' => now(),
            'approver_id' => $admin->id
        ]);
    }

    public function getIsDeclinedAttribute(): bool
    {
        return ! is_null($this->declined_at) && is_null($this->approved_at);
    }

    public function getIsDeclinableAttribute(): bool
    {
        return !$this->is_approved && !$this->is_declined;
    }

    public function decline(Admin $admin)
    {
        if($this->is_declined)
        {
            throw ValidationException::withMessages([
                'payout' => 'This payout has already been declined.'
            ]);
        }

        return $this->update([
            'declined_at' => now(),
            'decliner_id' => $admin->id
        ]);
    }
}
