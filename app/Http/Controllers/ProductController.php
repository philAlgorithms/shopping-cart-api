<?php

namespace App\Http\Controllers;

use App\Models\Products\Product;
use App\Http\Requests\Validators\ProductValidator;
use App\Http\Resources\Products\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $store = request('store_id');
        $brand = request('brand_id');
        $subCategory = request('sub_category');
        $subCategoryId = request('sub_category_id');
        $category = request('category');
        $categoryId = request('category_id');
        $products = Product::query()
            ->when(
                request('tags') && is_array(request('tags')),
                fn ($builder) => $builder->whereHas(
                    'tags',
                    fn ($builder) => $builder->whereIn('tag_id', request('tags'))
                )
            )
            ->when(
                request('reviews') && is_array(request('reviews')),
                fn ($builder) => $builder->whereHasMorph(
                    'reviews',
                    fn ($builder) => $builder->whereIn('rateable_id', request('reviews'))
                )
            )
            ->when(
                $store && (is_array($store) || is_numeric($store)),
                fn ($builder) => $builder->whereIn('store_id', is_array($store) ? $store : [$store])
            )
            ->when(
                $brand && (is_array($brand) || is_numeric($brand)),
                fn ($builder) => $builder->whereIn('brand_id', is_array($brand) ? $brand : [$brand])
            )
            ->when(
                $subCategoryId && (is_array($subCategoryId) || is_numeric($subCategoryId)),
                fn ($builder) => $builder->whereIn('product_sub_category_id', is_array($subCategoryId) ? $subCategory : [$subCategory])
            )
            ->when(
                $subCategory && (is_array($subCategory) || is_string($subCategory)),
                fn ($builder) => $builder->whereHas(
                    'subCategory',
                    fn ($query) => $query->whereIn('name', is_array($subCategory) ? $subCategory : [$subCategory])
                )
            )
            ->when(
                $category && (is_array($category) || is_string($category)),
                fn ($builder) => $builder->whereHas(
                    'subCategory',
                    fn ($query) => $query->whereHas(
                        'category',
                        fn ($q) => $q->whereIn('name', is_array($category) ? $category : [$category])
                    )
                )
            )
            ->when(
                $categoryId && (is_array($categoryId) || is_numeric($categoryId)),
                fn ($builder) => $builder->whereHas(
                    'subCategory',
                    fn ($query) => $query->whereHas(
                        'category',
                        fn ($q) => $q->whereIn('name', is_array($categoryId) ? $categoryId : [$categoryId])
                    )
                )
            )
            ->with(['coverImage', 'brand', 'store', 'subCategory'])
            ->paginate(getpaginator(request()));

        return ProductResource::collection(
            $products
        );
    }

    /**
     * Create a new product resource
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function create()
    {
        $this->authorize('create', [Product::class]);

        $validated = (new ProductValidator)->validate(
            $product = new Product([
                'store_id' => auth()->user()->store->id
            ]),
            request()
        );

        $product = DB::transaction(function () use (
            $product,
            $validated
        ) {
            if (array_key_exists('cover_image', $validated)) {
                $uploaded_cover_image = request()->file('cover_image');
                $cover_image = fillMediaFile(uploaded_file: $uploaded_cover_image, disk: env('DEFAULT_DISK', 'local'), path: 'products/category');
            }
            $prepared = Arr::except($validated, ['cover_image', 'tags', 'images']);

            !isset($cover_image) ?: $cover_image->save();

            $prepared['cover_image_id'] = $cover_image->id ?? null;

            $product->fill($prepared);
            $product->save();

            $product->tags()->attach($validated['tags'] ?? []);

            if (array_key_exists('images', $validated)) {
                foreach (request()->file('images') as $uploaded_image) {
                    $image = fillMediaFile(uploaded_file: $uploaded_image, disk: env('DEFAULT_DISK', 'local'), path: "products/{$product->id}");
                    if ($image->save()) {
                        $product->images()->attach($image->id);
                    }
                }
            }

            return $product;
        });

        return ProductResource::make(
            $product->load(['brand', 'store', 'coverImage'])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Skills\Skill  $skill
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Product $product)
    {
        $product //->loadCount(['reservations' => fn($builder) => $builder->where('status', Reservation::STATUS_ACTIVE)])
            ->load(['coverImage', 'tags', 'store', 'subCategory', 'reviews']);

        return ProductResource::make($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Products\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Product $product)
    {
        $this->authorize('create', [Product::class]);
        $this->authorize('update', $product);

        $validated = (new ProductValidator)->validate($product, request());

        $product = DB::transaction(function () use ($product, $validated) {
            $prepared = Arr::except($validated, ['cover_image']);

            if (array_key_exists('cover_image', $validated)) {
                $uploaded_cover_image = request()->file('cover_image');
                $cover_image = saveOrUpdateMediaFile(
                    media_file: $product->coverImage,
                    uploaded_file: $uploaded_cover_image,
                    disk: env('DEFAULT_DISK', 'local'),
                    path: "products/{$product->id}",
                    delete_media: true,
                    callback: function ($model) use ($prepared) {
                        // $model->save();
                    }
                );
                $prepared['cover_image_id'] = $cover_image->id;
            }

            $product->fill($prepared);
            $product->save();

            return $product;
        });

        return ProductResource::make(
            $product
        );
    }

    /**
     * Remove the specified resource from public view.
     *
     * @param  \App\Models\Products\Product  $skill
     */
    public function delete(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
    }

    /**
     * Remove the product entirely.
     *
     * @param  \App\Models\Products\Product  $product
     */
    public function forceDelete(Product $product)
    {
        $this->authorize('forceDelete', $product);
        $product->forceDelete();
    }
}
