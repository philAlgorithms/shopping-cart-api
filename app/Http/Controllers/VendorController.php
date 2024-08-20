<?php

namespace App\Http\Controllers;

use App\Models\Users\Vendor;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Http\Resources\Users\VendorResource;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendors = Vendor::query()
                ->paginate(getpaginator(request()));

        return VendorResource::collection(
            $vendors
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        return VendorResource::make(
            $vendor
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVendorRequest  $request
     * @param  \App\Models\Users\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        //
    }
}
