<?php

namespace App\Models\Products;

use App\Models\Media\{MediaFile};
use App\Models\{Tag};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphToMany};
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * Gets the logo of this brand
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'logo_id');
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo->temporary_url ?? '';
    }

    /**
     * Gets the tags associated with this brand
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Products by this brand
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
