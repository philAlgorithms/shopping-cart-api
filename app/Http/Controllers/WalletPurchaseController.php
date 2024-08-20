<?php

namespace App\Http\Controllers;

use App\Models\Payments\WalletPurchase;
use App\Http\Requests\StoreWalletPurchaseRequest;
use App\Http\Requests\UpdateWalletPurchaseRequest;

class WalletPurchaseController extends Controller
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
     * @param  \App\Http\Requests\StoreWalletPurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWalletPurchaseRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payments\WalletPurchase  $walletPurchase
     * @return \Illuminate\Http\Response
     */
    public function show(WalletPurchase $walletPurchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payments\WalletPurchase  $walletPurchase
     * @return \Illuminate\Http\Response
     */
    public function edit(WalletPurchase $walletPurchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWalletPurchaseRequest  $request
     * @param  \App\Models\Payments\WalletPurchase  $walletPurchase
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWalletPurchaseRequest $request, WalletPurchase $walletPurchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payments\WalletPurchase  $walletPurchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(WalletPurchase $walletPurchase)
    {
        //
    }

    public function pay(WalletPurchase $walletPurchase)
    {
        return $walletPurchase->pay();
    }
}
