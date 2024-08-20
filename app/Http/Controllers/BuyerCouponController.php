<?php

namespace App\Http\Controllers;

use App\Models\BuyerCoupon;
use App\Http\Requests\StoreBuyerCouponRequest;
use App\Http\Requests\UpdateBuyerCouponRequest;

class BuyerCouponController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBuyerCouponRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBuyerCouponRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BuyerCoupon  $buyerCoupon
     * @return \Illuminate\Http\Response
     */
    public function show(BuyerCoupon $buyerCoupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BuyerCoupon  $buyerCoupon
     * @return \Illuminate\Http\Response
     */
    public function edit(BuyerCoupon $buyerCoupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBuyerCouponRequest  $request
     * @param  \App\Models\BuyerCoupon  $buyerCoupon
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBuyerCouponRequest $request, BuyerCoupon $buyerCoupon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BuyerCoupon  $buyerCoupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(BuyerCoupon $buyerCoupon)
    {
        //
    }
}
