<?php

namespace App\Http\Controllers;

use App\Models\Order\Journey;
use App\Http\Requests\StoreJourneyRequest;
use App\Http\Requests\UpdateJourneyRequest;
use App\Http\Requests\Validators\JourneyValidator;
use App\Http\Resources\Order\{JourneyResource};
use App\Models\Location\{Town};
use App\Models\Order\OrderJourney;
use App\Models\Users\{LogisticsPersonnel};
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JourneyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $is_assigned = request('is_assigned');
        $journeys = Journey::query()
            ->when(
                auth()->user() instanceof LogisticsPersonnel,
                fn ($builder) => $builder->where('logistics_personnel_id', auth()->id())
            )
            ->when(
                request()->exists('is_assigned') && (is_bool($is_assigned) || $is_assigned === '1' || $is_assigned === '0'),
                function($builder) use($is_assigned) {
                    if((bool)$is_assigned)
                    {
                        return $builder->whereNotNull('logistics_personnel_id');
                    }else
                    {
                        return $builder->whereNull('logistics_personnel_id'); 
                    }
                }
            )
            ->paginate(getpaginator(request()));

        return JourneyResource::collection(
            $journeys
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Journey $journey): JsonResource
    {
        return JourneyResource::make(
            $journey->load(['origin', 'destination', 'itinerary', 'logisticsPersonnel'])
        );
    }

    /**
     * Create a new journey resource
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function create()
    {
        // $this->authorize('create', [Journey::class]);

        $validated = (new JourneyValidator)->validate(
            $journey = new Journey(),
            request()->all()
        );

        $this->authorize(
            'assignManyToJourney', 
            [
                OrderJourney::class,
                OrderJourney::wherein('id', array_key_exists('waybills', $validated) ? $validated['waybills'] : [])->get()
            ]
        );

        $journey = DB::transaction(function () use (
            $journey,
            $validated
        ) {
            // $journey->fill(['logistics_personnel_id' => auth()->id(), ...Arr::except($validated, ['waybills'])]);
            $journey->fill(Arr::except($validated, ['waybills']));
            $journey->save();

            return $journey;
        });

        $waybills = array_key_exists('waybills', $validated) ? OrderJourney::wherein('id', $validated['waybills']) : [];

        foreach ($waybills as $waybill) {
            $waybill->assignToJourney($journey);
        }

        return JourneyResource::make(
            $journey->load(['destination', 'origin'])
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreJourneyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJourneyRequest $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Response
     */
    public function edit(Journey $journey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateJourneyRequest  $request
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJourneyRequest $request, Journey $journey)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Journey $journey)
    {
        //
    }

    /**
     * Set the destination the journey.
     *
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function setOrigin(Journey $journey)
    {
        $validated = validator(
            request()->all(),
            [
                'town_id' => ['required', 'numeric', Rule::exists('towns', 'id')],
            ],
            [
                'town_id.required' => 'Please select a town.'
            ]
        )->validate();
        $town = Town::find($validated['town_id']);

        $this->authorize('setOrigin', [$journey, $town]);

        $journey->setOrigin($town);

        return JourneyResource::make(
            $journey->refresh()->load(['origin', 'destination', 'itinerary'])
        );
    }

    /**
     * Set the destination the journey.
     *
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function setDestination(Journey $journey)
    {
        $validated = validator(
            request()->all(),
            [
                'town_id' => ['required', 'numeric', Rule::exists('towns', 'id')],
            ],
            [
                'town_id.required' => 'Please select a town.'
            ]
        )->validate();
        $town = Town::find($validated['town_id']);

        $this->authorize('setDestination', [$journey, $town]);

        $journey->setDestination($town);

        return JourneyResource::make(
            $journey->refresh()->load(['origin', 'destination', 'itinerary'])
        );
    }

    /**
     * Set a checkpoint for the journey.
     *
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function setCheckpoint(Journey $journey)
    {
        $validated = validator(
            request()->all(),
            [
                'town_id' => ['required', 'numeric', Rule::exists('towns', 'id')],
            ],
            [
                'town_id.required' => 'Please select a town.'
            ]
        )->validate();
        $town = Town::find($validated['town_id']);

        $this->authorize('setCheckpoint', [$journey, $town]);

        $journey->setCheckpoint($town);

        return JourneyResource::make(
            $journey->refresh()->load(['origin', 'destination', 'itinerary'])
        );
    }

    /**
     * Assign a logistics personnel that will prside over the journey.
     *
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function assignLogisticsPersonnel(Journey $journey)
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

        $this->authorize('assignLogisticsPersonnel', [$journey, $logisticsPersonnel]);

        $journey->assignLogisticsPersonnel($logisticsPersonnel);

        return JourneyResource::make(
            $journey->refresh()->load(['logisticsPersonnel', 'origin', 'destination', 'itinerary'])
        );
    }

    /**
     * Mark journey as started.
     *
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function markAsLeft(Journey $journey)
    {
        $this->authorize('markAsLeft', [$journey]);

        $journey->markAsLeft();

        return JourneyResource::make(
            $journey->refresh()->load(['logisticsPersonnel', 'origin', 'destination', 'itinerary'])
        );
    }

    /**
     * Mark journey as started.
     *
     * @param  \App\Models\Order\Journey  $journey
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function markAsArrived(Journey $journey)
    {
        $this->authorize('markAsArrived', [$journey]);

        $journey->markAsArrived();

        return JourneyResource::make(
            $journey->refresh()->load(['logisticsPersonnel', 'origin', 'destination', 'itinerary'])
        );
    }
}
