<?php

namespace App\Http\Controllers;

use App\Models\Media\Disk;
use App\Http\Requests\StoreDiskRequest;
use App\Http\Requests\UpdateDiskRequest;

class DiskController extends Controller
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
     * @param  \App\Http\Requests\StoreDiskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDiskRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Media\Disk  $disk
     * @return \Illuminate\Http\Response
     */
    public function show(Disk $disk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Media\Disk  $disk
     * @return \Illuminate\Http\Response
     */
    public function edit(Disk $disk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDiskRequest  $request
     * @param  \App\Models\Media\Disk  $disk
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDiskRequest $request, Disk $disk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Media\Disk  $disk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Disk $disk)
    {
        //
    }
}
