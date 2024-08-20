<?php

namespace App\Http\Controllers;

use App\Models\Media\MediaFile;
use App\Http\Requests\StoreMediaFileRequest;
use App\Http\Requests\UpdateMediaFileRequest;

class MediaFileController extends Controller
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
     * @param  \App\Http\Requests\StoreMediaFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMediaFileRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Media\MediaFile  $mediaFile
     * @return \Illuminate\Http\Response
     */
    public function show(MediaFile $mediaFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Media\MediaFile  $mediaFile
     * @return \Illuminate\Http\Response
     */
    public function edit(MediaFile $mediaFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMediaFileRequest  $request
     * @param  \App\Models\Media\MediaFile  $mediaFile
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMediaFileRequest $request, MediaFile $mediaFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Media\MediaFile  $mediaFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(MediaFile $mediaFile)
    {
        //
    }
}
