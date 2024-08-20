<?php

namespace App\Http\Controllers;

use App\Models\Stores\Store;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Stores\{StoreFinanceResource, StoreResource};
use App\Models\Products\Product;
use App\Models\Users\Vendor;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::query()
            ->paginate(getpaginator(request()));

        return StoreResource::collection(
            $stores
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        return StoreResource::make(
            $store
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stores\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateStoreRequest  $request
     * @param  \App\Models\Stores\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStoreRequest $request, Store $store)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stores\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        //
    }

    public function uploadVerificationDocuments(Store $store)
    {
        $this->authorize('uploadVerificationDocuments', $store);

        $validated = validator(request(), [
            'cac' => ['required', 'mimes:pdf', 'max:5000'],
        ]);


        $uploaded_file = request()->file('cac');
        $file = fillMediaFile(uploaded_file: $uploaded_file, disk: env('DEFAULT_DISK', 'local'), path: 'projects/portfolio');

        $store->update([
            'cac_file_id' => $file->id
        ]);

        return StoreResource::make(
            $store->refresh()
        );
    }

    /**
     * Approve a store.
     *
     * @param  \App\Models\Stores\Store  $store
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function verify(Store $store): JsonResource|Response
    {
        $this->authorize('listProducts', $store);
        return $store->markAsVerified() ?
            StoreResource::make(
                $store->refresh()
            ) :
            response('Unable to verify. Try again later.', 409);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function listProducts(Store $store): JsonResource
    {
        $brand = request('brand_id');
        $subCategory = request('sub_category');
        $subCategoryId = request('sub_category_id');
        $category = request('category');
        $categoryId = request('category_id');
        $products = Product::query()
            ->where('store_id', $store->id)
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
                    fn ($query) => $q->whereIn('name', is_array($subCategory) ? $subCategory : [$subCategory])
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function listVendorProducts(): JsonResource
    {
        $storeId = auth()->user()->store->id ?? null;
        $brand = request('brand_id');
        $subCategory = request('sub_category');
        $subCategoryId = request('sub_category_id');
        $category = request('category');
        $categoryId = request('category_id');
        $products = Product::query()
            ->where('store_id', $storeId)
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
                $brand && (is_array($brand) || is_numeric($brand)),
                fn ($builder) => $builder->whereIn('brand_id', is_array($brand) ? $brand : [$brand])
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
                        fn ($q) => $query->whereIn('name', is_array($category) ? $category : [$category])
                    )
                )
            )
            ->when(
                $categoryId && (is_array($categoryId) || is_numeric($categoryId)),
                fn ($builder) => $builder->whereHas(
                    'subCategory',
                    fn ($query) => $query->whereHas(
                        'category',
                        fn ($q) => $query->whereIn('name', is_array($categoryId) ? $categoryId : [$categoryId])
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
     * Display the store finance.
     *
     * @param  \App\Models\Users\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function finance(Store $store)
    {
        return StoreFinanceResource::make(
            $store
        );
    }

    /**
     * Display the store finance.
     *
     * @param  \App\Models\Users\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function storeFinance()
    {
        $this->authorize('hasStore', [Vendor::class]);
        return StoreFinanceResource::make(
            auth()->user()->store
        );
    }
}
