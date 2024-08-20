<?php

namespace App\Models\Payments;

use App\Models\Currency;
use App\Models\Order\{Order};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

class Purchase extends Model
{
    use HasFactory;
    
    public function order(): BelongsTo
    {
        return $this->purchasable->order();
    }

    public function currency(): BelongsTo
    {
        return $this->purchasable->currency();
    }

    /**
     * Get the parent purchasable model (paystack or wallet purchase).
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }
}
