<?php

namespace App\Http\Controllers;

use App\Http\Resources\Products\{ProductResource};
use App\Http\Resources\Ratings\RatingResource;
use App\Models\Products\{Product};
use App\Models\Ratings\Rating;
use App\Models\Users\{Buyer};
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductRatingController extends Controller
{
    /**
     * Lists a product's reviews
     * 
     * @param \App\Models\Products\Product $product
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(Product $product)
    {
        $ratings = Rating::query()
            ->whereHasMorph('rateable', [Product::class], 
                fn ($builder) => $builder->where('id', $product->id)
            )
            ->paginate(getpaginator(request()));
        
        return RatingResource::collection(
            $ratings
        );
    }

    
    /**
     * Shows a product rating
     * 
     * @param \App\Models\Products\Product $product
     * @param \App\Models\Ratings\Rating $rating
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Product $product, Rating $rating): JsonResource
    {
        $rating->load('rateable');

        return RatingResource::make(
            $rating
        );
    }

    
    /**
     * Rates a product
     * 
     * @param \App\Models\Products\Product $product
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function create(Product $product): JsonResource
    {
        $this->authorize('rate', $product);

        $validated = validator(request()->all(),
            [
                'title' => ['required', 'string', 'max: 40'],
                'comment' => ['required', 'string'],
            ]
        )->validate();

        // User must have bought product
        $user = Buyer::find(auth()->id())->user;

        $review = $product->reviews()->create([
            'rater_id' => $user->id,
            'comment_title' => $validated['title'],
            'comment' => $validated['comment'],
        ]);

        return RatingResource::make(
            $review->load(['rateable'])
        );
    }

    /**
     * Remove the specified resource from public view.
     *
     * @param  \App\Models\Products\Product  $skill
     */
    public function delete(Product $product, Rating $rating)
    {
        $this->authorize('delete', $rating);

        return $rating->delete();
    }

    /**
     * Remove the product entirely.
     *
     * @param  \App\Models\Products\Product  $product
     */
    public function forceDelete(Product $product, Rating $rating)
    {
        $this->authorize('forceDelete', $rating);
        
        return $rating->forceDelete();
    }
}
