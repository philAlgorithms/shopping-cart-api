<?php

namespace App\Models\Products;

use App\Models\Media\{MediaFile};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class ProductSubCategory extends Model
{
    use HasFactory;

    /**
     * Gets the category which a sub category of a product belongs to
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
         return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * Gets the category which a sub category of a product belongs to
     * 
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
         return $this->hasMany(Product::class);
    }

    /**
     * Gets the tags associated with this product sub category
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Gets the icon of this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function icon(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'icon_id');
    }

    public function getIconUrlAttribute(): string
    {
        return $this->icon->temporary_url ?? '';
    }

    /**
     * Gets the cover image of this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'cover_image_id');
    }

    public function getCoverImageUrlAttribute(): string
    {
        return $this->coverImage->temporary_url ?? '';
    }
}
