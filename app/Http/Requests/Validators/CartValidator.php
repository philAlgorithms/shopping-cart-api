<?php
namespace App\Http\Requests\Validators;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CartValidator
{
    public function validateEdit(array $attributes): array
    {
        return validator(
            $attributes,
            [
                'product_id' => ['required', Rule::exists('products', 'id')],
                'quantity' => ['required', 'numeric', 'min:1']
            ],
            [
                'product_id.required' => 'A product is required.',
                'product_id.exists' => 'Product does not exist.',
                'quantity.min' => 'Product quantity must be at least 1'
            ]
        )->validate();
    }
}