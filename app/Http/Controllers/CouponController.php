<?php

namespace App\Http\Controllers;

use App\Models\Order\Coupon;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Requests\Validators\CouponValidator;
use App\Http\Resources\Order\CouponResource;
use App\Models\Users\{Vendor};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', [Coupon::class]);
        $user = auth()->user();
        $code = request('code');

        $coupons = Coupon::query()
            ->when(
                $user instanceof Vendor,
                fn ($builder) => $builder->whereHas(
                    'store',
                    fn ($builder) => $builder->where('vendor_id', 'like', $user->id)
                )
            )
            ->when(
                $code && is_string($code),
                fn ($builder) => $builder->where('code', 'like', "{$code}%")
            )
            ->paginate(getpaginator(request()));

        return CouponResource::collection(
            $coupons
        );
    }

    /**
     * Create a new product resource
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function create()
    {
        $validated = (new CouponValidator)->validate(
            $coupon = new Coupon(),
            request()->all()
        );
        $request_contains_products = array_key_exists('products', $validated);
        $user = auth()->user();
        $user_is_vendor = $user instanceof Vendor;

        $this->authorize(
            'create',
            [
                Coupon::class,
                $request_contains_products ? $validated['products'] : []
            ]
        );

        $coupon = DB::transaction(function () use (
            $coupon,
            $user,
            $validated,
            $user_is_vendor,
            $request_contains_products
        ) {
            $prepared = Arr::except($validated, ['products']);

            if ($user_is_vendor) $prepared['store_id'] = $user->store->id ?? null;

            $coupon->fill($prepared);
            $coupon->save();

            if ($user_is_vendor && $request_contains_products) {
                $coupon->products()->attach($validated['products'] ?? []);
            }

            return $coupon;
        });

        return CouponResource::make(
            $coupon->load(['store', 'products'])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        $this->authorize('view', $coupon);

        return CouponResource::make(
            $coupon
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCouponRequest  $request
     * @param  \App\Models\Order\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(Coupon $coupon)
    {
        $this->authorize(
            'update',
            $coupon
        );

        $validated = (new CouponValidator)->validate(
            $coupon = $coupon,
            request()->all()
        );
        
        $coupon = DB::transaction(function () use (
            $coupon,
            $validated,
        ) {
            $prepared = Arr::except($validated, ['products', 'code']);


            $coupon->fill($prepared);
            $coupon->save();

            return $coupon;
        });

        return CouponResource::make(
            $coupon->load(['store', 'products'])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        //
    }
}
