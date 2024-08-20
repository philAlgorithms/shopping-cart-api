<?php

namespace App\Http\Controllers;

use App\Models\CouponRequest;
use App\Http\Requests\StoreCouponRequestRequest;
use App\Http\Requests\UpdateCouponRequestRequest;
use App\Http\Resources\Order\CouponRequestResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CouponRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(
            'request',
            [
                CouponRequest::class,
            ]
        );

        $coupon = DB::transaction(function () {
            $coupon = CouponRequest::create([
                'buyer_id' => auth()->id()
            ]);
            return $coupon;
        });

        return CouponRequestResource::make(
            $coupon->load(['buyer'])
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCouponRequestRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCouponRequestRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CouponRequest  $couponRequest
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(CouponRequest $couponRequest)
    {
        $this->authorize('show', $couponRequest);

        return CouponRequestResource::make(
            $couponRequest->load(['buyer', 'coupon'])
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CouponRequest  $couponRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(CouponRequest $couponRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCouponRequestRequest  $request
     * @param  \App\Models\CouponRequest  $couponRequest
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCouponRequestRequest $request, CouponRequest $couponRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CouponRequest  $couponRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(CouponRequest $couponRequest)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\CouponRequest  $couponRequest
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function approve(CouponRequest $couponRequest)
    {
        $this->authorize(
            'approve',
            $couponRequest
        );
        
        $validated = validator(
            request()->all(),
            [
                'coupon_id' => ['required', 'numeric', Rule::exists('coupons', 'id')],
            ],
            [
                'coupon_id.required' => 'Please select a coupon.',
                'coupon_id.exists' => 'The selected coupon does not exist.'
            ]
        )->validate();

        $couponRequest->approve(CouponRequest::find($validated['coupon_id']));

        return CouponRequestResource::make(
            $couponRequest->load(['buyer', 'coupon'])
        );
    }
}
