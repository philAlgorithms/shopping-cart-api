<?php
namespace App\Http\Requests\Validators;

use App\Models\StorePayout;
use Illuminate\Validation\Rule;

class StorePayoutValidator
{
    public function validate(StorePayout $payout, array $attributes): array
    {
        return validator(
            $attributes,
            [
                'amount' => ['required', 'numeric', 'min:5000'],
            ],
            [
                'amount.min' => 'Amount must not be less than 5,000 NGN.'
            ]
        )->validate();
    }
}