<?php

namespace App\Models\Order;

use App\Models\Location\Town;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class OrderJourney extends Model
{
    use HasFactory;

    public function journey(): BelongsTo
    {
        return $this->belongsTo(Journey::class, 'journey_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // START BOOLS
    public function getWasDeliveredAttribute(): bool
    {
        return !is_null($this->delivered_at) && $this->delivered_at < now();
    }

    public function getWasReceivedAttribute(): bool
    {
        return !is_null($this->received_at) && $this->received_at < now();
    }

    public function getIsEditableAttribute(): bool
    {
        return !($this->was_delivered || $this->was_received);
    }

    public function getHasArrivedAttribute(): bool
    {
        return !is_null($this->journey) && $this->journey->has_arrived;
    }

    public function getHasLeftAttribute(): bool
    {
        return !is_null($this->journey) && $this->journey->has_left;
    }

    public function getHasJourneyAttribute(): bool
    {
        return !is_null($this->journey);
    }

    public function getStatusAttribute(): string
    {
        if ($this->was_received) {
            return 'RECEIVED';
        } else if ($this->was_delivered) {
            return 'DELIVERED';
        } else if ($this->has_arrived) {
            return 'ARRIVED';
        } else if ($this->has_left) {
            return 'IN_TRANSIT';
        } else if ($this->has_journey) {
            return 'PROCESSED';
        } else {
            return 'PROCESSING';
        }
    }
    // END BOOLS


    // START ACTIONS

    public function updateJourney(Journey $journey): bool
    {
        if (!$this->is_editable) {
            return false;
        } else {
            return $this->update([
                'journey_id' => $journey->id
            ]);
        }
    }

    public function markAsDelivered(): bool
    {
        if ($this->was_delivered) {
            return false;
        } else {
            return $this->update([
                'delivered_at' => now()
            ]);
        }
    }

    public function markAsReceived(): bool
    {
        if ($this->was_received) {
            return false;
        } else {
            return $this->update([
                'received_at' => now()
            ]);
        }
    }

    public function updateCost(float $amount): bool
    {
        // Return false if the order has been paid for at all.
        if (!$this->is_editable || $this->order->amount_paid > 0) {
            return false;
        } else {
            return $this->update([
                'cost' => $amount
            ]);
        }
    }

    // public function updateTown(Town $town): bool
    // {
    //     if (!$this->is_editable) {
    //         return false;
    //     } else {
    //         if ($this->has_journey && !$this->journey->has_left) {
    //             return $this->update([
    //                 'town_id' => $town->id
    //             ]);
    //         }

    //         return false;
    //     }
    // }

    public function assignToJourney(Journey $journey): bool
    {
        // You cannot update orders that has been delivered
        // or if order was not paid in full
        // or if the journey desination is not the same as the order's delivery town
        if (
            $this->was_delivered ||
            $this->was_received ||
            !$this->order->has_paid_full ||
            ($journey->destination_town_id !== $this->order->delivery_town_id)
        ) {
            return false;
        } else {
            return $this->update([
                'journey_id' => $journey->id
            ]);
        }
    }

    // END ACTIONS
}
