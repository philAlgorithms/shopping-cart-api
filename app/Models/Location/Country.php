<?php

namespace App\Models\Location;

use App\Models\{Currency, User};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};

class Country extends Model
{
    use HasFactory;

    public function states():HasMany
    {
        return $this->hasMany(State::class);
    }
    
    /**
     * Gets this country's currencies
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function currencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class, 'country_currencies');
    }

    /**
     * Users that are from this country
     * 
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
