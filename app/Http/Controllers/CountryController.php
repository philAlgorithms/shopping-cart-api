<?php

namespace App\Http\Controllers;

use App\Models\Location\Country;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\Location\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $countries = Country::query()
                ->without(['states'])
                ->paginate(getpaginator(request(), 240));

        return CountryResource::collection(
            $countries
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location\Country  $country
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Country $country): JsonResource
    {
        return CountryResource::make(
            $country->load(['states'])
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCountryRequest  $request
     * @param  \App\Models\Location\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCountryRequest $request, Country $country)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        //
    }
}
