<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class Town extends Model
{
    use HasFactory;

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }
}
