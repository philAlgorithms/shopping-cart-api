<?php

namespace App\Http\Controllers;

use App\Models\Order\{Order, OrderJourney};
use App\Http\Requests\StoreOrderJourneyRequest;
use App\Http\Requests\UpdateOrderJourneyRequest;
use App\Http\Resources\Order\{OrderJourneyResource, OrderResource};
use App\Models\Users\Buyer;
use App\Models\Users\LogisticsPersonnel;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;

class OrderJourneyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $user = auth()->user();
        $is_delivered = request('is_delivered');
        $is_received = request('is_received');
        $town_id = request('town_id');
        $orderJourneys = OrderJourney::query()
        ->when(
            $user instanceof Buyer,
            fn ($builder) => $builder->whereHas(
                'order',
                fn ($b) => $b->where('buyer_id', $user->id)
            )
        )
        ->when(
            $user instanceof LogisticsPersonnel,
            fn ($builder) => $builder->whereHas(
                'journey',
                fn ($b) => $b->where('logistics_personnel_id', $user->id)
            )
        )
        ->when(
            $town_id && (is_array($town_id) || is_numeric($town_id)),
            fn ($builder) => $builder->whereHas(
                'order',
                fn ($b) => $b->whereIn('delivery_town_id', is_array($town_id) ? $town_id : [$town_id])
            )
        )
        ->when(
            request()->exists('is_delivered') && (is_bool($is_delivered) || $is_delivered === '1' || $is_delivered === '0'),
            function($builder) use($is_delivered) {
                if((bool)$is_delivered)
                {
                    return $builder->whereNotNull('delivered_at');
                }else
                {
                    return $builder->whereNull('delivered_at'); 
                }
            }
        )
        ->when(
            request()->exists('is_received') && (is_bool($is_received) || $is_received === '1' || $is_received === '0' || $is_received === 'true' || $is_received === 'false'),
            function($builder) use($is_received) {
                $is_received = $is_received === 'false' ? false : $is_received;
                if((bool)$is_received)
                {
                    return $builder->whereNotNull('received_at');
                }else
                {
                    return $builder->whereNull('received_at'); 
                }
            }
        )
            ->paginate(getpaginator(request()));

        return OrderJourneyResource::collection(
            $orderJourneys
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(OrderJourney $orderJourney): JsonResource
    {
        $this->authorize('view', [$orderJourney]);
        return OrderJourneyResource::make(
            $orderJourney
        );
    }

    /**
     * Create a new journey resource
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function create(Order $order)
    {
        $this->authorize('initiateWaybill', $order);

        $order->initiateWaybill();

        return OrderResource::make(
            $order->refresh()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderJourneyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderJourneyRequest $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderJourney $orderJourney)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderJourneyRequest  $request
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderJourneyRequest $request, OrderJourney $orderJourney)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderJourney $orderJourney)
    {
        //
    }

    /**
     * Update the journey of this waybill.
     *
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function assignToJourney(OrderJourney $orderJourney)
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

        $this->authorize('assignToJourney', [$orderJourney, $journey]);

        $orderJourney->assignToJourney($journey);

        return OrderJourneyResource::make(
            $orderJourney->refresh()->load(['journey', 'origin', 'destination', 'itinerary'])
        );
    }

    /**
     * Mark journey as delivered.
     *
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function markAsDelivered(OrderJourney $orderJourney)
    {
        $this->authorize('markAsDelivered', [$orderJourney]);

        $orderJourney->markAsDelivered();

        return OrderJourneyResource::make(
            $orderJourney->refresh()->load(['journey', 'origin', 'destination', 'itinerary'])
        );
    }

    /**
     * Mark journey as received.
     *
     * @param  \App\Models\Order\OrderJourney  $orderJourney
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function markAsReceived(OrderJourney $orderJourney)
    {
        $this->authorize('markAsReceived', [$orderJourney]);

        $orderJourney->markAsReceived();

        return OrderJourneyResource::make(
            $orderJourney->refresh()->load(['journey', 'origin', 'destination', 'itinerary'])
        );
    }
}
