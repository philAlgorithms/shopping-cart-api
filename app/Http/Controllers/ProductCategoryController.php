<?php

namespace App\Http\Controllers;

use App\Models\Products\{ProductCategory};
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Http\Requests\Validators\ProductCategoryValidator;
use App\Http\Resources\Products\{ProductCategoryResource, ProductResource};
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $sub_category = is_countable(request('sub-category')) ? request('sub-category') : [request('sub-category')];
        $categories = ProductCategory::query()
            ->when(request('tags'),
                fn($builder) => $builder->whereHas(
                    'tags',
                    fn ($builder) => $builder->whereIn('tag_id', request('tags'))
                )->with('tags')
            )
            ->when(request('sub_category'),
                fn($builder) => $builder->whereHas(
                    'subCategories',
                    fn($builder) => $builder->whereIn('id', $sub_category)
                )->with('subCategories')
            )
            ->when(request('products'),
                fn($builder) => $builder->whereHas(
                    'Products',
                    fn($builder) => $builder->whereIn('id', request('products'))
                )->with('products')
            )
            ->with(['subCategories'])
            ->paginate(getPaginator(request()));

        return ProductCategoryResource::collection(
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
        $this->authorize('create', [ProductCategory::class]);
        
        $validated = (new ProductCategoryValidator)->validate(
            $productCategory = new ProductCategory(), 
            request()->all()
        );

        $productCategory = DB::transaction(function () use (
            $productCategory, $validated
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

            $productCategory->fill($prepared);
            $productCategory->save();

            // $productCategory->tags()->attach($validated['tags'] ?? []);

            return $productCategory;
        });

        return ProductCategoryResource::make(
            $productCategory->load(['icon', 'coverImage'])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Products\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategory $productCategory)
    {
        return ProductCategoryResource::make(
            $productCategory
        );
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Products\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function update(ProductCategory $productCategory)
    {
        $this->authorize('create', [ProductCategory::class]);
        $this->authorize('update', $productCategory);
        
        $validated = (new ProductCategoryValidator)->validate($productCategory, request()->all());

        $productCategory = DB::transaction(function () use ($productCategory, $validated) {
            $prepared = Arr::except($validated, ['cover_image', 'icon']);
    
            if(array_key_exists('cover_image', $validated))
            {
                $uploaded_cover_image = request()->file('cover_image');
                $cover_image = saveOrUpdateMediaFile(
                    media_file: $productCategory->coverImage,
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
                    media_file: $productCategory->coverImage,
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

            $productCategory->fill($prepared);
            $productCategory->save();

            return $productCategory;
        });

        return ProductCategoryResource::make(
            $productCategory
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Products\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function delete(ProductCategory $productCategory)
    {
        $this->authorize('delete', $productCategory);

        if($productCategory->subCategories()->count() > 0)
        {
            throw ValidationException::withMessages([
                'product_category' => 'Cannot delete a category that has existing sub categories.'
            ]);
        }
        // Delete everything concerning the product category including the image models and their associated files
        $coverImage = $productCategory->coverImage;
        $icon = $productCategory->icon; 
        $productCategory->delete();

        if(! is_null($coverImage)) $coverImage->remove();
        if(! is_null($icon)) $icon->remove();
    }
}
