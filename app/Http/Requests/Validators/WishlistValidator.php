<?php
namespace App\Http\Requests\Validators;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class WishlistValidator
{
    public function validate(array $attributes): array
    {
        return validator(
            $attributes,
            [
                'product_id' => ['required', Rule::exists('products', 'id')],
            ],
            [
                'product_id.required' => 'A product is required.',
                'product_id.exists' => 'Product does not exist.'
            ]
        )->validate();
    }
}