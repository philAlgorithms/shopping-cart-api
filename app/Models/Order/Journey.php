<?php

namespace App\Models\Order;

use App\Models\Location\{Town};
use App\Models\Users\{LogisticsPersonnel};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};

class Journey extends Model
{
    use HasFactory;

    public function logisticsPersonnel(): BelongsTo
    {
        return $this->belongsTo(LogisticsPersonnel::class, 'logistics_personnel_id');
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Town::class, 'origin_town_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Town::class, 'destination_town_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_journeys');
    }

    public function orderJourneys(): HasMany
    {
        return $this->hasMany(OrderJourney::class, 'journey_id');
    }

    public function itinerary(): HasMany
    {
        return $this->hasMany(Itinerary::class, 'journey_id');
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

    public function getHasLogisticsPersonnelAttribute(): bool
    {
        return !is_null($this->logisticsPersonnel);
    }

    public function getIsEditableAttribute(): bool
    {
        return !($this->has_arrived);
    }

    // END BOOLS

    // START ACTIONS

    public function markAsLeft(): bool
    {
        if ($this->has_left || !$this->has_logistics_personnel) {
            return false;
        } else {
            return $this->update([
                'left_at' => now()
            ]);
        }
    }

    public function autoAssignOrders()
    {
        // Find orders that have no waybill or whose waybill has not
        // been assigned to any trip add them to this trip
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

    public function assignLogisticsPersonnel(LogisticsPersonnel $logisticsPersonnel): bool
    {
        // You cannot update orders that has been delivered
        if ($this->has_arrived) {
            return false;
        } else {
            return $this->update([
                'logistics_personnel_id' => $logisticsPersonnel->id
            ]);
        }
    }

    public function setOrigin(Town $town): bool
    {
        // You cannot set origin town if the journey has started
        // or if the town is same as destination
        if ($this->has_left || $town->id == $this->destination->id ?? null) {
            return false;
        } else {
            return $this->update([
                'origin_town_id' => $town->id
            ]);
        }
    }

    public function setDestination(Town $town): bool
    {
        // You cannot set destination if the journey has already has orders
        // or if the town is same as origin
        if (
            $this->orders()->count() > 0 ||
            $town->id == $this->origin->id ?? null
        ) {
            return false;
        } else {
            return $this->update([
                'destination_town_id' => $town->id
            ]);
        }
    }

    public function setCheckpoint(Town $town): Model | false
    {
        // You cannot set a checkpoint if the journey has not started
        // or if the journey has already reached its end
        // or if the town is same as destination
        // or if the town is same as origin
        if (
            !$this->has_left ||
            $this->has_arrived ||
            $town->id == $this->destination->id ||
            $town->id == $this->origin->id
        ) {
            return false;
        } else {
            return $this->itinerary()->create([
                'town_id' => $town->id,
                'arrived_at' => now()
            ]);
        }
    }

    public function getStatusAttribute(): string
    {
        if ($this->has_arrived) {
            return 'ARRIVED';
        } else if ($this->has_left) {
            return 'IN_TRANSIT';
        } else {
            return 'PROCESSING';
        }
    }

    // END ACTIONS
}
