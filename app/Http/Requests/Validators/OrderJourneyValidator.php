<?php
namespace App\Http\Requests\Validators;

use App\Models\Order\OrderJourney;
use Illuminate\Validation\Rule;

class OrderJourneyValidator
{
    public function validate(OrderJourney $orderJourney, array $attributes): array
    {
        $exists = $orderJourney->exists;
        return validator(
            $attributes,
            [
                'origin_town_id' => [Rule::when($exists, 'sometimes'), 'required', 'numeric', Rule::exists('towns', 'id')],
                'destination_town_id' => [Rule::when($exists, 'sometimes'), 'required', 'numeric', Rule::exists('towns', 'id'), 'different:origin_town_id'],
            ],
            [
                'origin_town_id.required' => 'Please select the origin.',
                'desination_town_id.required' => 'Please select the desination.',
                'desination_town_id.different' => 'Destination cannot be the same as origin.',
            ]
        )->validate();
    }
}