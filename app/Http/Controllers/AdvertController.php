<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Http\Requests\StoreAdvertRequest;
use App\Http\Requests\UpdateAdvertRequest;
use App\Http\Requests\Validators\AdvertValidator;
use App\Http\Resources\AdvertResource;
use App\Models\Users\Admin;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AdvertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $adverts = Advert::query()
                ->paginate(getpaginator(request()));

        return AdvertResource::collection(
            $adverts
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', [Advert::class]);
        
        $validated = (new AdvertValidator)->validate(
            $advert = new Advert(), 
            request()->all()
        );

        $advert = DB::transaction(function () use (
            $advert, $validated
        ) {
            if(array_key_exists('image', $validated))
            {
                $uploaded_image = request()->file('image');
                $image = fillMediaFile(uploaded_file: $uploaded_image, disk: env('DEFAULT_DISK', 'local'), path: 'projects/portfolio');
            }
            $prepared = Arr::except($validated, ['image']);

            !isset($image) ?: $image->save();

            $prepared['image_id'] = $image->id ?? null;
            $advert->fill($prepared);
            $advert->save();

            // $advert->tags()->attach($validated['tags'] ?? []);

            return $advert;
        });

        return AdvertResource::make(
            $advert->load([])
        );
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Http\Response
     */
    public function show(Advert $advert)
    {
        return AdvertResource::make(
            $advert
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdvertRequest  $request
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Http\Response
     */
    public function update(Advert $advert)
    {
        $this->authorize('create', [Advert::class]);
        $this->authorize('update', $advert);
        
        $validated = (new AdvertValidator)->validate($advert, request()->all());

        $advert = DB::transaction(function () use ($advert, $validated) {
            $prepared = Arr::except($validated, ['image']);
    
            if(array_key_exists('image', $validated))
            {
                $uploaded_image = request()->file('image');
                $image = saveOrUpdateMediaFile(
                    media_file: $advert->coverImage,
                    uploaded_file: $uploaded_image, 
                    disk: env('DEFAULT_DISK', 'local'), 
                    path: 'projects/portfolio',
                    delete_media: true,
                    callback: function($model) use($prepared) {
                        // $model->save();
                    }
                );
                $prepared['image_id'] = $image->id;
            }

            $advert->fill($prepared);
            $advert->save();

            return $advert;
        });

        return AdvertResource::make(
            $advert
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Http\Response
     */
    public function delete(Advert $advert)
    {
        $this->authorize('delete', $advert);
        // Delete everything concerning the advert including the image model and its associated file
        $image = $advert->image; 
        $advert->delete();

        if(! is_null($image))  $image->remove();
    }
}
