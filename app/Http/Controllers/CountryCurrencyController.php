<?php

namespace App\Http\Controllers;

use App\Models\CountryCurrency;
use App\Http\Requests\StoreCountryCurrencyRequest;
use App\Http\Requests\UpdateCountryCurrencyRequest;

class CountryCurrencyController extends Controller
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
     * @param  \App\Http\Requests\StoreCountryCurrencyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCountryCurrencyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CountryCurrency  $countryCurrency
     * @return \Illuminate\Http\Response
     */
    public function show(CountryCurrency $countryCurrency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CountryCurrency  $countryCurrency
     * @return \Illuminate\Http\Response
     */
    public function edit(CountryCurrency $countryCurrency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCountryCurrencyRequest  $request
     * @param  \App\Models\CountryCurrency  $countryCurrency
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCountryCurrencyRequest $request, CountryCurrency $countryCurrency)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CountryCurrency  $countryCurrency
     * @return \Illuminate\Http\Response
     */
    public function destroy(CountryCurrency $countryCurrency)
    {
        //
    }
}
