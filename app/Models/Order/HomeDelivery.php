<?php

namespace App\Models\Order;

use App\Models\Location\{Town};
use App\Models\Users\{LogisticsPersonnel};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class HomeDelivery extends Model
{
    use HasFactory;

    public function logisticsPersonnel(): BelongsTo
    {
        return $this->belongsTo(LogisticsPersonnel::class, 'logistics_personnel_id');
    }

    public function town(): BelongsTo
    {
        return $this->order->deliveryTown();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // START BOOLS
    public function getHasLeftAttribute(): bool
    {
        return !is_null($this->left_at) && $this->left_at < now();
    }

    public function getHasArrivedAttribute(): bool
    {
        return !is_null($this->arrived_at) && $this->arrived_at < now();
    }

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

    public function getHasLogisticsPersonnelAttribute(): bool
    {
        return ! is_null($this->logisticsPersonnel);
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

    public function markAsLeft(): bool
    {
        if ($this->has_left) {
            return false;
        } else {
            // $this->assignLogisticsPersonnel($logisticsPersonnel);

            return $this->update([
                'left_at' => now()
            ]);
        }
    }

    public function markAsArrived(): bool
    {
        if ($this->has_arrived || !$this->has_left) {
            return false;
        } else {
            return $this->update([
                'arrived_at' => now()
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

    public function assignLogisticsPersonnel(LogisticsPersonnel $logisticsPersonnel): bool
    {
        // You cannot update orders that has been delivered
        if ($this->was_delivered || $this->was_received) {
            return false;
        } else {
            return $this->update([
                'logistics_personnel_id' => $logisticsPersonnel->id,
                'origin_address' => $logisticsPersonnel->base_address
            ]);
        }
    }

    public function assignToJourney(Journey $journey): bool
    {
        // You cannot update orders that has been delivered
        // or if order was not paid in full
        // or if the journey desination is not the same as the order's delivery town
        if (
            $this->was_delivered ||
            $this->was_received ||
            !$this->order->has_paid_full ||
            $this->journey->destination_town_id !== $this->order->delivery_town_id
        ) {
            return false;
        } else {
            return $this->update([
                'journey_id' => $journey->id
            ]);
        }
    }

    public function updateOrigin(string $address): bool
    {
        // Return false if the order has been paid for at all.
        if (!$this->is_editable) {
            return false;
        } else {
            return $this->update([
                'origin_address' => $address
            ]);
        }
    }

    // END ACTIONS
}
