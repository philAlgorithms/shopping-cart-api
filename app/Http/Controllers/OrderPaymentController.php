<?php

namespace App\Http\Controllers;

use App\Models\Payments\OrderPayment;
use App\Http\Requests\StoreOrderPaymentRequest;
use App\Http\Requests\UpdateOrderPaymentRequest;

class OrderPaymentController extends Controller
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
     * @param  \App\Http\Requests\StoreOrderPaymentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderPaymentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payments\OrderPayment  $orderPayment
     * @return \Illuminate\Http\Response
     */
    public function show(OrderPayment $orderPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payments\OrderPayment  $orderPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderPayment $orderPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderPaymentRequest  $request
     * @param  \App\Models\Payments\OrderPayment  $orderPayment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderPaymentRequest $request, OrderPayment $orderPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payments\OrderPayment  $orderPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderPayment $orderPayment)
    {
        //
    }
}
