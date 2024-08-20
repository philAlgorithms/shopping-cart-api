<?php

namespace App\Http\Controllers;

use App\Models\Media\Mime;
use App\Http\Requests\StoreMimeRequest;
use App\Http\Requests\UpdateMimeRequest;

class MimeController extends Controller
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
     * @param  \App\Http\Requests\StoreMimeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMimeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Media\Mime  $mime
     * @return \Illuminate\Http\Response
     */
    public function show(Mime $mime)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Media\Mime  $mime
     * @return \Illuminate\Http\Response
     */
    public function edit(Mime $mime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMimeRequest  $request
     * @param  \App\Models\Media\Mime  $mime
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMimeRequest $request, Mime $mime)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Media\Mime  $mime
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mime $mime)
    {
        //
    }
}
