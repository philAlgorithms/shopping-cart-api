<?php

namespace App\Models\Specifications;

use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSpecification extends Model
{
    use HasFactory;

    /**
     * Gets the product of this pivot
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Gets the specification of this pivot
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specification(): BelongsTo
    {
        return $this->belongsTo(Specification::class);
    }
}
