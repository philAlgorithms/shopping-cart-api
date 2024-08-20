<?php
namespace App\Http\Requests\Validators;

use App\Models\Media\{MediaFile};
use App\Models\Products\{Product};
use App\Models\Specifications\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class SpecificationValidator
{
    public function validate(Specification $specification, array $attributes):array
    {
        return validator(
            $attributes,
            [
                'name' => [Rule::when($specification->exists, 'sometimes'), 'required', 'string', Rule::unique('specifications', 'name')],
                'icon' => [Rule::when($specification->exists, 'sometimes'), 'required', 'string'],
            ]
        )->validate();
    }
}