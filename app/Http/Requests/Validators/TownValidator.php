<?php

namespace App\Http\Requests\Validators;

use App\Models\Media\{MediaFile};
use App\Models\Location\{Town};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class TownValidator
{
    public function validate(Town $town, Request $request): array
    {
        $attributes = $request->all();
        return validator(
            $attributes,
            [
                'name' => [Rule::when($town->exists, 'sometimes'), 'required', 'string'],
                'state_id' => [Rule::when($town->exists, 'sometimes'), 'required', 'integer', Rule::exists('states', 'id')],
                'tags' => ['sometimes', 'array'],
                'tags.*' => ['integer', Rule::exists('tags', 'id')]
            ],
            [
                'state_id.required' => 'Please select a state',
                'state_id.exists' => 'The selected state does not exist'
            ]
        )->validate();
    }
}
