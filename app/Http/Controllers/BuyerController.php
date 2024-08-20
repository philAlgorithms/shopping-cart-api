<?php

namespace App\Http\Controllers;

use App\Models\Users\Buyer;
use App\Http\Requests\StoreBuyerRequest;
use App\Http\Requests\UpdateBuyerRequest;
use App\Http\Resources\Users\BuyerResource;

class BuyerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buyers = Buyer::query()
                ->paginate(getpaginator(request()));

        return BuyerResource::collection(
            $buyers
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users\Buyer  $buyer
     * @return \Illuminate\Http\Response
     */
    public function show(Buyer $buyer)
    {
        return BuyerResource::make(
            $buyer
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users\Buyer  $buyer
     * @return \Illuminate\Http\Response
     */
    public function edit(Buyer $buyer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBuyerRequest  $request
     * @param  \App\Models\Users\Buyer  $buyer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBuyerRequest $request, Buyer $buyer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users\Buyer  $buyer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Buyer $buyer)
    {
        //
    }
}
