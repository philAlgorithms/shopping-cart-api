<?php

namespace App\Http\Controllers;

use App\Models\Payments\PaystackPurchase;
use App\Http\Requests\StorePaystackPurchaseRequest;
use App\Http\Requests\UpdatePaystackPurchaseRequest;

class PaystackPurchaseController extends Controller
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
     * @param  \App\Http\Requests\StorePaystackPurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaystackPurchaseRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Http\Response
     */
    public function show(PaystackPurchase $paystackPurchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Http\Response
     */
    public function edit(PaystackPurchase $paystackPurchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePaystackPurchaseRequest  $request
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaystackPurchaseRequest $request, PaystackPurchase $paystackPurchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaystackPurchase $paystackPurchase)
    {
        //
    }

    public function pay(PaystackPurchase $paystackPurchase)
    {
        return $paystackPurchase->pay();
    }
}
