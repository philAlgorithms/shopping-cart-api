<?php

namespace App\Models;

use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * Gets the products assigned to this tag
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphByMany(Product::class, 'taggable');
    }

    /**
     * Gets the product sub categories assigned to this tag
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function productSubCategories(): MorphToMany
    {
        return $this->morphByMany(ProductCategory::class, 'taggable');
    }

    /**
     * Gets the product categories assigned to this tag
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function productCategories(): MorphToMany
    {
        return $this->morphByMany(ProductCategory::class, 'taggable');
    }
}
