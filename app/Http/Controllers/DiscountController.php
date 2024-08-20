<?php

namespace App\Http\Controllers;

use App\Models\Products\Discount;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Http\Requests\Validators\DiscountValidator;
use App\Http\Resources\Products\DiscountResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $discounts = Discount::query()
            ->when(isVendor(auth()->user()),
                fn($builder) => $builder->whereHas(
                    'vendor',
                    auth()->user()->id
                )
            )
            ->when(request('products') && is_array(request('products')),
                fn($builder) => $builder->whereIn(
                    'id', request('products')
                )->with('product')
            )
            ->paginate(getPaginator(request()));

        return DiscountResource::collection(
            $discounts
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', [Discount::class]);
        
        $validated = (new DiscountValidator)->validate(
            $discount = new Discount(),
            request()->all()
        );
        $discount->fill([
            'product_id' => $validated['product_id']
        ]);
        $product = $discount->product;

        /**
         * When one tries to create a new discount for a product that already has
         * discount and has not yet been purchased, the syatem will only update the
         * old discount based on the new discount parameters. This is so because creating
         * a new discount requires archiving any other discount relating to the product;
         * This archiving action is required for transaction records. Hence if the product
         * has not yet been bought, it means no one has used the discount and there is no
         * need to archive it.
         */
        if($product->has_discount && $product->has_been_bought)
            return $this->update($product->discount);

        $discount = DB::transaction(function () use (
            $discount, $product, $validated
        ) {
            /**
             * Now archive any existing discount for this product
             */
            foreach($product->allDiscounts as $oldDiscount)
            {
                $oldDiscount->delete();
            }

            $discount->fill($validated);
            $discount->save();

            return $discount;
        });

        return DiscountResource::make(
            $discount->load(['product'])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Discounts\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function show(Discount $discount)
    {
        $this->authorize('show', $discount);

        return DiscountResource::make(
            $discount->load(['product'])
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Products\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function update( Discount $discount)
    {
        $this->authorize('update', $discount);
        
        $validated = (new DiscountValidator)->validate($discount, request());

        $discount = DB::transaction(function () use ($discount, $validated) {
            $discount->fill($validated);
            $discount->save();

            return $discount;
        });

        return DiscountResource::make(
            $discount
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Discounts\Discount  $discount
     * @return \Illuminate\Http\Response
     */
    public function destroy(Discount $discount)
    {
        //
    }
}
