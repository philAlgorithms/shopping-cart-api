<?php

namespace App\Http\Controllers;

use App\Models\Products\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Requests\Validators\BrandValidator;
use App\Http\Resources\Products\BrandResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $brands = Brand::query()
            ->when(request('tags'),
                fn($builder) => $builder->whereHas(
                    'tags',
                    fn ($builder) => $builder->whereIn('tag_id', request('tags'))
                )->with('tags')
            )
            ->when(request('products'),
                fn($builder) => $builder->whereHas(
                    'products',
                    fn($builder) => $builder->whereIn('product_id', request('products'))
                )->with('products')
            )
            ->with(['logo'])
            ->paginate(getpaginator(request()));

        return BrandResource::collection(
            $brands
        );
    }

    /**
     * Create a new product resource
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function createLegacy()
    {
        $this->authorize('create', [Brand::class]);
        $prepared = (new BrandValidator)->validateAndPrepare($brand = new Brand(), request());
        
        $brand = DB::transaction(function() use(
            $brand, $prepared
        ) {
            if(array_key_exists('uploaded_logo', $prepared))
            {
                $logo = $prepared['uploaded_logo'];
                $logo->save();
                $prepared['logo_id'] = $logo->id;
            }

            $brand->fill(
                Arr::except($prepared, ['tags', 'uploaded_logo'])
            )->save();
            $brand->tags()->attach($prepared['tags'] ?? []);

            return $brand;
        });

        return BrandResource::make($brand->load(['logo', 'tags']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', [Brand::class]);
        
        $validated = (new BrandValidator)->validate(
            $brand = new Brand(), 
            request()
        );

        $brand = DB::transaction(function () use (
            $brand, $validated
        ) {
            if(array_key_exists('logo', $validated))
            {
                $uploaded_logo = request()->file('logo');
                $logo = fillMediaFile(uploaded_file: $uploaded_logo, disk: env('DEFAULT_DISK', 'local'), path: 'products/category');
            }
            $prepared = Arr::except($validated, ['logo', 'tags']);

            !isset($logo) ?: $logo->save();

            $prepared['logo_id'] = $logo->id ?? null;

            $brand->fill($prepared);
            $brand->save();

            // $brand->tags()->attach($validated['tags'] ?? []);

            return $brand;
        });

        return BrandResource::make(
            $brand->load(['logo'])
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBrandRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Products\Brand  $brand
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Brand $brand)
    {
        $brand
            ->load(['logo', 'products', 'tags']);

        return BrandResource::make($brand);
    }

    /**
     * Updates an existing brand resource
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function updateLegacy(Brand $brand)
    {
        $this->authorize('update', $brand);
        $prepared = (new BrandValidator)->validateAndPrepare($brand, request());
        
        $brand = DB::transaction(function() use(
            $brand, $prepared
        ) {
            if(array_key_exists('uploaded_logo', $prepared))
            {
                $logo = $prepared['uploaded_logo'];
                $logo->save();
                $prepared['logo_id'] = $logo->id;
            }

            $brand->fill(
                Arr::except($prepared, ['tags', 'uploaded_logo'])
            )->save();
            $brand->tags()->syncWithoutDetaching($prepared['tags'] ?? []);

            return $brand;
        });

        return BrandResource::make($brand->load(['logo', 'tags']));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Products\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Brand $brand)
    {
        $this->authorize('create', [Brand::class]);
        $this->authorize('update', $brand);
        
        $validated = (new BrandValidator)->validate($brand, request());

        $brand = DB::transaction(function () use ($brand, $validated) {
            $prepared = Arr::except($validated, ['tags', 'logo']);
    
            if(array_key_exists('logo', $validated))
            {
                $uploaded_logo = request()->file('logo');
                $logo = saveOrUpdateMediaFile(
                    media_file: $brand->coverImage,
                    uploaded_file: $uploaded_logo, 
                    disk: env('DEFAULT_DISK', 'local'), 
                    path: 'projects/portfolio',
                    delete_media: true,
                    callback: function($model) use($prepared) {
                        // $model->save();
                    }
                );
                $prepared['logo_id'] = $logo->id;
            }

            $brand->fill($prepared);
            $brand->save();

            return $brand;
        });

        return BrandResource::make(
            $brand
        );
    }

    /**
     * Remove the specified resource from public view.
     *
     * @param  \App\Models\Products\Brand  $brand
     */
    public function delete(Brand $brand)
    {
        $this->authorize('delete', $brand);
        $brand->delete();
    }

    /**
     * Remove the brand entirely.
     *
     * @param  \App\Models\Products\Brand  $brand
     */
    public function forceDelete(Brand $brand)
    {
        $this->authorize('forceDelete', $brand);

        $logo = $brand->logo;

        $brand->forceDelete();
        if(! is_null($logo)) $logo->remove();
    }
}
