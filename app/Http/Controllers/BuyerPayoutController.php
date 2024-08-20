<?php

namespace App\Http\Controllers;

use App\Models\BuyerPayout;
use App\Http\Requests\StoreBuyerPayoutRequest;
use App\Http\Requests\UpdateBuyerPayoutRequest;
use App\Http\Resources\BuyerPayoutResource;
use App\Models\Users\Buyer;

class BuyerPayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $payouts = BuyerPayout::query()
            ->when(
                $user instanceof Buyer,
                fn ($builder) => $builder->where('buyer_id', $user->id)
            )
            ->paginate(getpaginator(request(), 20));

        return BuyerPayoutResource::collection(
            $payouts
        );
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
     * @param  \App\Http\Requests\StoreBuyerPayoutRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBuyerPayoutRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BuyerPayout  $buyerPayout
     * @return \Illuminate\Http\Response
     */
    public function show(BuyerPayout $buyerPayout)
    {
        return BuyerPayoutResource::make(
            $buyerPayout
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BuyerPayout  $buyerPayout
     * @return \Illuminate\Http\Response
     */
    public function edit(BuyerPayout $buyerPayout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBuyerPayoutRequest  $request
     * @param  \App\Models\BuyerPayout  $buyerPayout
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBuyerPayoutRequest $request, BuyerPayout $buyerPayout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BuyerPayout  $buyerPayout
     * @return \Illuminate\Http\Response
     */
    public function destroy(BuyerPayout $buyerPayout)
    {
        //
    }

    /**
     * Approve a payout request.
     *
     * @param  \App\Models\BuyerPayout  $buyerPayout
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function approve(BuyerPayout $buyerPayout)
    {
        $this->authorize(
            'approve',
            $buyerPayout
        );

        $buyerPayout->approve(auth()->user());

        return BuyerPayoutResource::make(
            $buyerPayout
        );
    }

    /**
     * Decline a payout request.
     *
     * @param  \App\Models\BuyerPayout  $buyerPayout
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function decline(BuyerPayout $buyerPayout)
    {
        $this->authorize(
            'decline',
            $buyerPayout
        );

        $buyerPayout->decline(auth()->user());

        return BuyerPayoutResource::make(
            $buyerPayout
        );
    }
}
