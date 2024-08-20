<?php

namespace App\Http\Controllers;

use App\Models\TimeUnit;
use App\Http\Requests\StoreTimeUnitRequest;
use App\Http\Requests\UpdateTimeUnitRequest;

class TimeUnitController extends Controller
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
     * @param  \App\Http\Requests\StoreTimeUnitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimeUnitRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeUnit  $timeUnit
     * @return \Illuminate\Http\Response
     */
    public function show(TimeUnit $timeUnit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeUnit  $timeUnit
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeUnit $timeUnit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTimeUnitRequest  $request
     * @param  \App\Models\TimeUnit  $timeUnit
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimeUnitRequest $request, TimeUnit $timeUnit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeUnit  $timeUnit
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeUnit $timeUnit)
    {
        //
    }
}
