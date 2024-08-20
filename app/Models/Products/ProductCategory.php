<?php

namespace App\Models\Products;

use App\Models\Media\{MediaFile};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough, MorphToMany};

class ProductCategory extends Model
{
    use HasFactory;

    /**
     * Gets the sub categories of this product category.
     * 
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(ProductSubCategory::class, 'product_category_id');
    }

    /**
     * Gets the products in this category.
     * 
     * @return Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, ProductSubCategory::class);
    }

    /**
     * Gets the tags associated with this product category
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
     * Gets the cover image of this product category
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
