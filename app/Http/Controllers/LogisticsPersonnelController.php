<?php

namespace App\Http\Controllers;

use App\Models\Users\LogisticsPersonnel;
use App\Http\Requests\StoreLogisticsPersonnelRequest;
use App\Http\Requests\UpdateLogisticsPersonnelRequest;
use App\Http\Resources\Users\LogisticsPersonnelResource;

class LogisticsPersonnelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logisticsPersonnels = LogisticsPersonnel::query()
                ->paginate(getpaginator(request()));

        return LogisticsPersonnelResource::collection(
            $logisticsPersonnels
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
     * @param  \App\Http\Requests\StoreLogisticsPersonnelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLogisticsPersonnelRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users\LogisticsPersonnel  $logisticsPersonnel
     * @return \Illuminate\Http\Response
     */
    public function show(LogisticsPersonnel $logisticsPersonnel)
    {
        return LogisticsPersonnelResource::make(
            $logisticsPersonnel
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users\LogisticsPersonnel  $logisticsPersonnel
     * @return \Illuminate\Http\Response
     */
    public function edit(LogisticsPersonnel $logisticsPersonnel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLogisticsPersonnelRequest  $request
     * @param  \App\Models\Users\LogisticsPersonnel  $logisticsPersonnel
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLogisticsPersonnelRequest $request, LogisticsPersonnel $logisticsPersonnel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users\LogisticsPersonnel  $logisticsPersonnel
     * @return \Illuminate\Http\Response
     */
    public function destroy(LogisticsPersonnel $logisticsPersonnel)
    {
        //
    }
}
