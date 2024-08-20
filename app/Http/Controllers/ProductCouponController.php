<?php

namespace App\Http\Controllers;

use App\Models\ProductCoupon;
use App\Http\Requests\StoreProductCouponRequest;
use App\Http\Requests\UpdateProductCouponRequest;

class ProductCouponController extends Controller
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
     * @param  \App\Http\Requests\StoreProductCouponRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductCouponRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductCoupon  $productCoupon
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCoupon $productCoupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductCoupon  $productCoupon
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCoupon $productCoupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductCouponRequest  $request
     * @param  \App\Models\ProductCoupon  $productCoupon
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductCouponRequest $request, ProductCoupon $productCoupon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductCoupon  $productCoupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductCoupon $productCoupon)
    {
        //
    }
}
