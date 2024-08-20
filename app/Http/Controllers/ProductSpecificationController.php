<?php

namespace App\Http\Controllers;

use App\Models\Specifications\ProductSpecification;
use App\Http\Requests\StoreProductSpecificationRequest;
use App\Http\Requests\UpdateProductSpecificationRequest;

class ProductSpecificationController extends Controller
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
     * @param  \App\Http\Requests\StoreProductSpecificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductSpecificationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Specifications\ProductSpecification  $productSpecification
     * @return \Illuminate\Http\Response
     */
    public function show(ProductSpecification $productSpecification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Specifications\ProductSpecification  $productSpecification
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductSpecification $productSpecification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductSpecificationRequest  $request
     * @param  \App\Models\Specifications\ProductSpecification  $productSpecification
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductSpecificationRequest $request, ProductSpecification $productSpecification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Specifications\ProductSpecification  $productSpecification
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductSpecification $productSpecification)
    {
        //
    }
}
