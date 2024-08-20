<?php
namespace App\Http\Requests\Validators;

use App\Models\Media\{MediaFile};
use App\Models\Products\{Product, ProductCategory};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProductCategoryValidator
{
    public function validate(ProductCategory $category, array $attributes):array
    {
        $exists = $category->exists;
        return validator(
            $attributes,
            [
                'name' => [Rule::when($exists, 'sometimes'), 'required', 'string'],
                'icon' => [Rule::when($exists, 'sometimes'), 'sometimes', 'image', 'mimes:png,jpg,jpeg,svg', 'max:5000'],
                'tags' => ['sometimes', 'array'],
                'cover_image' => [Rule::when($exists, 'sometimes'), 'sometimes', 'image', 'mimes:png,jpg,jpeg,svg', 'max:5000'],
                'tags' => ['sometimes', 'array'],
                'tags.*' => ['integer', Rule::exists('tags', 'id')]
            ]
        )->validate();
    }
}