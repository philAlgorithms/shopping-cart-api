<?php

namespace App\Http\Requests\Validators;

use App\Models\Order\Journey;
use Illuminate\Validation\Rule;

class JourneyValidator
{
    public function validate(Journey $journey, array $attributes): array
    {
        $exists = $journey->exists;
        return validator(
            $attributes,
            [
                'origin_town_id' => [Rule::when($exists, 'sometimes'), 'required', 'numeric', Rule::exists('towns', 'id')],
                'destination_town_id' => [Rule::when($exists, 'sometimes'), 'required', 'numeric', Rule::exists('towns', 'id'), 'different:origin_town_id'],
                'logistics_personnel_id' => ['required', 'numeric', Rule::exists('logistics_personnels', 'id')],

                'waybills' => ['sometimes', 'array'],
                'waybills.*' => ['integer', Rule::exists('order_journeys', 'id')],
            ],
            [
                'origin_town_id.required' => 'Please select the origin.',
                'desination_town_id.required' => 'Please select the desination.',
                'desination_town_id.different' => 'Destination cannot be the same as origin.',
                'logistics_personnel_id.required' => 'Please select a logistics_personnel.'
            ]
        )->validate();
    }
}
