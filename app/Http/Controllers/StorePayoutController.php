<?php

namespace App\Http\Controllers;

use App\Models\StorePayout;
use App\Http\Requests\StoreStorePayoutRequest;
use App\Http\Requests\UpdateStorePayoutRequest;
use App\Http\Requests\Validators\StorePayoutValidator;
use App\Http\Resources\StorePayoutResource;
use App\Models\Stores\Store;
use App\Models\Users\Vendor;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StorePayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $payouts = StorePayout::query()
            ->when(
                $user instanceof Vendor,
                fn ($builder) => $builder->whereHas(
                    'store',
                    fn ($query) => $query->where('vendor_id', $user->id)
                )
            )
            ->paginate(getpaginator(request(), 20));

        return StorePayoutResource::collection(
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
        $validated = (new StorePayoutValidator)->validate(
            $payout = new StorePayout(),
            request()->all()
        );

        $this->authorize('create', [StorePayout::class, $validated['amount']]);

        $payout = DB::transaction(function () use (
            $payout,
            $validated
        ) {
            $validated['store_id'] = auth()->user()->store->id;
            $payout->fill($validated);
            $payout->save();

            return $payout;
        });

        return StorePayoutResource::make(
            $payout
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreStorePayoutRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStorePayoutRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Http\Response
     */
    public function show(StorePayout $storePayout)
    {
        return StorePayoutResource::make(
            $storePayout
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Http\Response
     */
    public function edit(StorePayout $storePayout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStorePayoutRequest  $request
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStorePayoutRequest $request, StorePayout $storePayout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Http\Response
     */
    public function destroy(StorePayout $storePayout)
    {
        $this->authorize(
            'delete',
            $storePayout
        );

        return $storePayout->delete();
    }

    /**
     * Approve a payout request.
     *
     * @param  \App\Models\StorePayout  $storePayout
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function approve(StorePayout $storePayout)
    {
        $this->authorize(
            'approve',
            $storePayout
        );

        $storePayout->approve(auth()->user());

        return StorePayoutResource::make(
            $storePayout
        );
    }

    /**
     * Decline a payout request.
     *
     * @param  \App\Models\StorePayout  $storePayout
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function decline(StorePayout $storePayout)
    {
        $this->authorize(
            'decline',
            $storePayout
        );

        $storePayout->decline(auth()->user());

        return StorePayoutResource::make(
            $storePayout
        );
    }
}
