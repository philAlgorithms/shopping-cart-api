<?php

namespace App\Http\Controllers;

use App\Models\BuyerReferral;
use App\Http\Requests\StoreBuyerReferralRequest;
use App\Http\Requests\UpdateBuyerReferralRequest;
use App\Http\Resources\BuyerReferralResource;
use App\Models\Users\Buyer;

class BuyerReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $referrals = BuyerReferral::query()
                ->when(
                    auth()->user() instanceof Buyer,
                    fn($builder) => $builder->whereHas(
                        'program',
                        fn($query) => $query->where('buyer_id', auth()->id())
                    )
                )
                ->paginate(getpaginator(request()));

        return BuyerReferralResource::collection(
            $referrals
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
     * @param  \App\Http\Requests\StoreBuyerReferralRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBuyerReferralRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BuyerReferral  $buyerReferral
     * @return \Illuminate\Http\Response
     */
    public function show(BuyerReferral $buyerReferral)
    {
        $this->authorize('view', $buyerReferral);

        return BuyerReferralResource::make(
            $buyerReferral
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BuyerReferral  $buyerReferral
     * @return \Illuminate\Http\Response
     */
    public function edit(BuyerReferral $buyerReferral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBuyerReferralRequest  $request
     * @param  \App\Models\BuyerReferral  $buyerReferral
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBuyerReferralRequest $request, BuyerReferral $buyerReferral)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BuyerReferral  $buyerReferral
     * @return \Illuminate\Http\Response
     */
    public function destroy(BuyerReferral $buyerReferral)
    {
        //
    }
}
