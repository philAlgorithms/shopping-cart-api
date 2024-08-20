<?php

namespace App\Http\Controllers;

use App\Models\Products\{ProductSubCategory};
use App\Http\Requests\StoreProductSubCategoryRequest;
use App\Http\Requests\UpdateProductSubCategoryRequest;
use App\Http\Requests\Validators\ProductSubCategoryValidator;
use App\Http\Resources\Products\{ProductSubCategoryResource, ProductResource};
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $category = request('category');
        $categories = ProductSubCategory::query()
            ->when(request('tags'),
                fn($builder) => $builder->whereHas(
                    'tags',
                    fn ($builder) => $builder->whereIn('tag_id', request('tags'))
                )->with('tags')
            )
            ->when(request('product'),
                fn($builder) => $builder->whereHas(
                    'products',
                    fn($builder) => $builder->whereIn('id', request('product'))
                )
            )
            ->when($category && (is_array($category) || is_numeric($category)),
                fn($builder) => $builder->whereIn('product_category_id', is_array($category) ? $category : [$category])
            )
            ->paginate(
                getPaginator(request())
            );

        return ProductSubCategoryResource::collection(
            $categories
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', [ProductSubCategory::class]);
        
        $validated = (new ProductSubCategoryValidator)->validate(
            $productSubCategory = new ProductSubCategory(), 
            request()->all()
        );

        $productSubCategory = DB::transaction(function () use (
            $productSubCategory, $validated
        ) {
            if(array_key_exists('icon', $validated))
            {
                $uploaded_icon = request()->file('icon');
                $icon = fillMediaFile(uploaded_file: $uploaded_icon, disk: env('DEFAULT_DISK', 'local'), path: 'products/category');
            }
            if(array_key_exists('cover_image', $validated))
            {
                $uploaded_cover_image = request()->file('cover_image');
                $cover_image = fillMediaFile(uploaded_file: $uploaded_cover_image, disk: env('DEFAULT_DISK', 'local'), path: 'products/category');
            }
            $prepared = Arr::except($validated, ['icon', 'cover_image']);

            !isset($icon) ?: $icon->save();
            !isset($cover_image) ?: $cover_image->save();

            $prepared['icon_id'] = $icon->id ?? null;
            $prepared['cover_image_id'] = $cover_image->id ?? null;

            $productSubCategory->fill($prepared);
            $productSubCategory->save();

            // $productSubCategory->tags()->attach($validated['tags'] ?? []);

            return $productSubCategory;
        });

        return ProductSubCategoryResource::make(
            $productSubCategory->load(['icon', 'coverImage'])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Products\ProductSubCategory  $productSubCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductSubCategory $productSubCategory)
    {
        return ProductSubCategoryResource::make(
            $productSubCategory
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Products\ProductSubCategory  $productSubCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductSubCategory $productSubCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Products\ProductSubCategory  $productSubCategory
     * @return \Illuminate\Http\Response
     */
    public function update(ProductSubCategory $productSubCategory)
    {
        $this->authorize('create', [ProductSubCategory::class]);
        $this->authorize('update', $productSubCategory);
        
        $validated = (new ProductSubCategoryValidator)->validate($productSubCategory, request()->all());

        $productSubCategory = DB::transaction(function () use ($productSubCategory, $validated) {
            $prepared = Arr::except($validated, ['cover_image', 'icon']);
    
            if(array_key_exists('cover_image', $validated))
            {
                $uploaded_cover_image = request()->file('cover_image');
                $cover_image = saveOrUpdateMediaFile(
                    media_file: $productSubCategory->coverImage,
                    uploaded_file: $uploaded_cover_image, 
                    disk: env('DEFAULT_DISK', 'local'), 
                    path: 'projects/portfolio',
                    delete_media: true,
                    callback: function($model) use($prepared) {
                        // $model->save();
                    }
                );
                $prepared['cover_image_id'] = $cover_image->id;
            } 
            if(array_key_exists('icon', $validated))
            {
                $uploaded_icon = request()->file('icon');
                $icon = saveOrUpdateMediaFile(
                    media_file: $productSubCategory->coverImage,
                    uploaded_file: $uploaded_icon, 
                    disk: env('DEFAULT_DISK', 'local'), 
                    path: 'projects/portfolio',
                    delete_media: true,
                    callback: function($model) use($prepared) {
                        // $model->save();
                    }
                );
                $prepared['icon_id'] = $icon->id;
            }

            $productSubCategory->fill($prepared);
            $productSubCategory->save();

            return $productSubCategory;
        });

        return ProductSubCategoryResource::make(
            $productSubCategory
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Products\ProductSubCategory  $productSubCategory
     * @return \Illuminate\Http\Response
     */
    public function delete(ProductSubCategory $productSubCategory)
    {
        $this->authorize('delete', $productSubCategory);

        if($productSubCategory->products()->count() > 0)
        {
            throw ValidationException::withMessages([
                'product_sub_category' => 'Cannot delete a sub-category that has existing products.'
            ]);
        }
        // Delete everything concerning the product category including the image models and their associated files
        $coverImage = $productSubCategory->coverImage;
        $icon = $productSubCategory->icon; 
        $productSubCategory->delete();

        if(! is_null($coverImage)) $coverImage->remove();
        if(! is_null($icon)) $icon->remove();
    }
}
