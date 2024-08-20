<?php
namespace App\Http\Requests\Validators;

use App\Models\Media\{MediaFile};
use App\Models\Products\{Product};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProductValidator
{
    public function validate(Product $product, Request $request):array
    {
        $attributes = $request->all();
        $base_validators = 
        [
            'name' => [Rule::when($product->exists, 'sometimes'), 'required', 'string'],
            'quantity' => [Rule::when($product->exists, 'sometimes'), 'required', 'integer', 'min:1'],
            'price' => [Rule::when($product->exists, 'sometimes'), 'required', 'numeric', 'min:5'],
            'product_sub_category_id' => [Rule::when($product->exists, 'sometimes'), 'required', 'integer', Rule::exists('product_sub_categories', 'id')],
            'brand_id' => ['sometimes', 'integer', Rule::exists('brands', 'id')],
            'cover_image' => [Rule::when($product->exists, 'sometimes'), 'required', 'image', 'mimes:png,jpg,jpeg,svg', 'max:5000'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['image', 'mimes:png,jpg,jpeg,svg', 'max:5000'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', Rule::exists('tags', 'id')],
            // 'specifications' => ['sometimes', 'array'],
            // 'specifications.*.id' => ['integer', Rule::exists('specifications', 'id')],
            // 'specifications.*.detail' => ['string']
        ];

        $validators = $product->exists ? $base_validators : array_merge(
            $base_validators,
            [
                'images' => ['sometimes', 'array'],
                'images.*' => ['image', 'mimes:png,jpg,jpeg,svg', 'max:5000'],
                'tags' => ['sometimes', 'array'],
                'tags.*' => ['integer', Rule::exists('tags', 'id')],
                // 'specifications' => ['sometimes', 'array'],
                // 'specifications.*.id' => ['integer', Rule::exists('specifications', 'id')],
                // 'specifications.*.detail' => ['string']
            ],
        );
        return validator(
            $attributes,
            $validators,
            [
                'product_sub_category_id.required' => 'Please select a sub category',
                'product_sub_category_id.exists' => 'The selected sub category does not exist',
                'brand_id.required' => 'Please select a brand',
                'brand_id.exists' => 'The selected brand does not exist',
                'images.array' => 'Product images must be a list of image files'
            ]
        )->validate();
    }

    public function validateAndPrepare(Product $product, Request $request): array
    {
        $validated = $this->validate($product, $request);
        $prepared = [];

        if(array_key_exists('specifications', $validated))
        {
            $specification_data = [];
            foreach($validated['specifications'] as $specification)
            {
                $specification_data[$specification['id']] = array(
                    'detail' => $specification['detail']
                );
            }
            $prepared['specifications'] = $specification_data;
        }

        $prepared = [
            ...Arr::except($validated, ['specifications']),
            ...$prepared
        ];

        return $prepared;
    }
}