<?php

namespace App\Http\Controllers;

use App\Models\State\StatusCategory;
use App\Http\Requests\StoreStatusCategoryRequest;
use App\Http\Requests\UpdateStatusCategoryRequest;

class StatusCategoryController extends Controller
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
     * @param  \App\Http\Requests\StoreStatusCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStatusCategoryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\State\StatusCategory  $statusCategory
     * @return \Illuminate\Http\Response
     */
    public function show(StatusCategory $statusCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\State\StatusCategory  $statusCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(StatusCategory $statusCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStatusCategoryRequest  $request
     * @param  \App\Models\State\StatusCategory  $statusCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStatusCategoryRequest $request, StatusCategory $statusCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\State\StatusCategory  $statusCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(StatusCategory $statusCategory)
    {
        //
    }
}
