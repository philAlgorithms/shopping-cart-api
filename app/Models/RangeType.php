<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RangeType extends Model
{
    use HasFactory;

    public function ranges(): HasMany
    {
        return $this->hasMany(Range::class, 'range_type_id');
    }

    public function getValue(float $amount)
    {
        $upper = $this->ranges()->where('minimum', '<=', $amount);

        if($upper->get()->count() > 0)
        {
            $lowerData = $this->ranges()->where('maximum', '>=', $amount)->get();

            if($lowerData->count() == 0)
            {
                return $upper->first()->value;
            }
            else {
                return $upper->where('maximum', '>=', $amount)->first()->value;
            }
        }
        return 0;
    }
}
