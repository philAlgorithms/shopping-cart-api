<?php
namespace App\Http\Requests\Validators;

use App\Models\Media\{MediaFile};
use App\Models\Order\{Coupon};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class CouponValidator
{
    public function validate(Coupon $coupon, array $attributes): array
    {
        $exists = $coupon->exists;
        return validator(
            $attributes,
            [
                'percentage' => [Rule::when($exists, 'sometimes'), 'required', 'numeric', 'min:0.1', 'max:95'],
                'count' => ['sometimes', 'numeric', 'min:1'],
                'code' => [Rule::when($exists, 'sometimes'), 'required', 'string', 'max:255', Rule::unique('coupons', 'code')],
                'is_active' => ['sometimes', 'boolean'],
                'expires_at' => [Rule::when($exists, 'sometimes'), 'required', 'date', 'date_format:Y-m-d H:i:s', 'after:today'],
                'products' => ['sometimes', 'array'],
                'products.*' => ['integer', Rule::exists('products', 'id')],
            ],
            [
                'products.*.exists' => 'Some selected products do not exist',
                'code.unique' => 'A coupon with this exact code already exists',
                'percentage.min' => 'The minimum discount for a coupon is :min %',
                'percentage.max' => 'The maximum discount for a coupon is :max %',             
                'expires_at.after' => 'The expiration date must be sometime in the future',      
                // 'expires_at.after' => 'The expiration date must be in the format :date_format'
            ]
        )->validate();
    }
}