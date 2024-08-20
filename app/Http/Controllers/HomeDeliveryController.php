<?php

namespace App\Http\Controllers;

use App\Models\Order\{HomeDelivery, Order};
use App\Http\Requests\StoreHomeDeliveryRequest;
use App\Http\Requests\UpdateHomeDeliveryRequest;
use App\Http\Resources\Order\{HomeDeliveryResource, OrderResource};
use App\Models\Users\LogisticsPersonnel;
use Illuminate\Validation\Rule;

class HomeDeliveryController extends Controller
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
     * Initiate home delivery
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Order $order)
    {
        $this->authorize('initiateHomeDelivery', $order);

        $order->initiateHomeDelivery();

        return OrderResource::make(
            $order->refresh()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Order $order)
    {
        $this->authorize('disableHomeDelivery', $order);

        $order->disableHomeDelivery();

        return OrderResource::make(
            $order->refresh()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreHomeDeliveryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHomeDeliveryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Response
     */
    public function show(HomeDelivery $homeDelivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Response
     */
    public function edit(HomeDelivery $homeDelivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHomeDeliveryRequest  $request
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHomeDeliveryRequest $request, HomeDelivery $homeDelivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Response
     */
    public function destroy(HomeDelivery $homeDelivery)
    {
        //
    }

    /**
     * Assign a logistics personnel that will prside over the journey.
     *
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function assignLogisticsPersonnel(HomeDelivery $homeDelivery)
    {
        $validated = validator(
            request()->all(),
            [
                'logistics_personnel_id' => ['required', 'numeric', Rule::exists('logistics_personnels', 'id')],
            ],
            [
                'logistics_personnel_id.required' => 'Please select a logistics_personnel.'
            ]
        )->validate();
        $logisticsPersonnel = LogisticsPersonnel::find($validated['logistics_personnel_id']);

        $this->authorize('assignLogisticsPersonnel', [$homeDelivery, $logisticsPersonnel]);

        $homeDelivery->assignLogisticsPersonnel($logisticsPersonnel);

        return HomeDeliveryResource::make(
            $homeDelivery->refresh()
        );
    }

    /**
     * Mark journey as started.
     *
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function markAsLeft(HomeDelivery $homeDelivery)
    {
        $this->authorize('markAsLeft', [$homeDelivery]);

        $homeDelivery->markAsLeft();

        return HomeDeliveryResource::make(
            $homeDelivery->refresh()
        );
    }

    /**
     * Mark journey as started.
     *
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function markAsArrived(HomeDelivery $homeDelivery)
    {
        $this->authorize('markAsArrived', [$homeDelivery]);

        $homeDelivery->markAsArrived();

        return HomeDeliveryResource::make(
            $homeDelivery->refresh()
        );
    }

    /**
     * Mark journey as delivered.
     *
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function markAsDelivered(HomeDelivery $homeDelivery)
    {
        $this->authorize('markAsDelivered', [$homeDelivery]);

        $homeDelivery->markAsDelivered();

        return HomeDeliveryResource::make(
            $homeDelivery->refresh()
        );
    }

    /**
     * Mark journey as received.
     *
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function markAsReceived(HomeDelivery $homeDelivery)
    {
        $this->authorize('markAsReceived', [$homeDelivery]);

        $homeDelivery->markAsReceived();

        return HomeDeliveryResource::make(
            $homeDelivery->refresh()
        );
    }

    /**
     * Update the journey of this waybill.
     *
     * @param  \App\Models\Order\HomeDelivery  $homeDelivery
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function updateJourney(HomeDelivery $homeDelivery)
    {
        $validated = validator(
            request()->all(),
            [
                'journey_id' => ['required', 'numeric', Rule::exists('journeys', 'id')],
            ],
            [
                'journey_id.required' => 'Please select a journey.'
            ]
        )->validate();
        $journey = LogisticsPersonnel::find($validated['logistics_personnel_id']);

        $this->authorize('updateJourney', [$homeDelivery, $journey]);

        $homeDelivery->updateJourney($journey);

        return HomeDeliveryResource::make(
            $homeDelivery->refresh()
        );
    }
}
