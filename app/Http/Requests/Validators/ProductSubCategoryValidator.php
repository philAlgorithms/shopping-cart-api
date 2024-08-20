<?php

namespace App\Http\Requests\Validators;

use App\Models\Products\{ProductSubCategory};
use Illuminate\Validation\Rule;

class ProductSubCategoryValidator
{
    public function validate(ProductSubCategory $category, array $attributes): array
    {
        $exists = $category->exists;
        return validator(
            $attributes,
            [
                'name' => [Rule::when($exists, 'sometimes'), 'required', 'string'],
                'icon' => [Rule::when($exists, 'sometimes'), 'sometimes', 'image', 'mimes:png,jpg,jpeg,svg', 'max:5000'],
                'product_category_id' => [Rule::when($exists, 'sometimes'), 'required', Rule::exists('product_categories', 'id')],
                'tags' => ['sometimes', 'array'],
                'cover_image' => [Rule::when($exists, 'sometimes'), 'sometimes', 'image', 'mimes:png,jpg,jpeg,svg', 'max:5000'],
                'tags' => ['sometimes', 'array'],
                'tags.*' => ['integer', Rule::exists('tags', 'id')]
            ]
        )->validate();
    }
}
