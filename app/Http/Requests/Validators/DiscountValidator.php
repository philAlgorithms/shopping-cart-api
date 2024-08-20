<?php
namespace App\Http\Requests\Validators;

use App\Models\Media\{MediaFile};
use App\Models\Products\{Discount};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class DiscountValidator
{
    public function validate(Discount $discount, array $attributes): array
    {
        $exists = $discount->exists;
        return validator(
            $attributes,
            [
                'product_id' => [Rule::when($exists, 'sometimes'), 'required', 'numeric', Rule::exists('products', 'id')],
                'percentage' => [Rule::when($exists, 'sometimes'), 'required', 'numeric', 'min:0.1', 'max:95'],
                'count' => [Rule::when($exists, 'sometimes'), 'numeric', 'min:1'],
                'expires_at' => [Rule::when($exists, 'sometimes'), 'required', 'date', 'date_format:Y-m-d H:i:s', 'after:today'],
            ],
            [
                'product_id.required' => 'Please select a product',
                'product_id.exists' => 'The selected product does not exist',

                'percentage.min' => 'The minimum discount should not be less than :min %',
                
                'expires_at.after' => 'The expiration date must be sometime in the future'
            ]
        )->validate();
    }
}