<?php

namespace App\Http\Controllers;

use App\Http\Requests\Validators\WishlistValidator;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\WishlistResource;
use App\Models\Products\{Product};
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistController extends Controller
{
    public function add(): JsonResource
    {
        $validated = (new WishlistValidator)->validate(request()->all());

        $product = Product::find($validated['product_id']);
        $wishList = $product->addToWishlist($validated['quantity']);

        return ProductResource::collection(
            $wishList->all()
        );
    }
}
