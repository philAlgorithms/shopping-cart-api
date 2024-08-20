<?php

namespace App\Models\Ratings;

use App\Models\Products\Product;
use App\Models\{User};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Gets the resource associated with this rating
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The reviewer
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_id');
    }
}
