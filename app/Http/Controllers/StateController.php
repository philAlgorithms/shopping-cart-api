<?php

namespace App\Http\Controllers;

use App\Models\Location\State;
use App\Http\Requests\UpdateStateRequest;
use App\Http\Resources\Location\StateResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $countries = State::query()
                ->without(['towns'])
                ->paginate(getpaginator(request(), 240));

        return StateResource::collection(
            $countries
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location\State  $country
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(State $country): JsonResource
    {
        return StateResource::make(
            $country->load(['states'])
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Location\State  $state
     * @return \Illuminate\Http\Response
     */
    public function edit(State $state)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStateRequest  $request
     * @param  \App\Models\Location\State  $state
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStateRequest $request, State $state)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location\State  $state
     * @return \Illuminate\Http\Response
     */
    public function destroy(State $state)
    {
        //
    }
}
