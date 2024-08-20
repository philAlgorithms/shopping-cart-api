<?php
namespace App\Http\Requests\Validators;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TagValidator
{
    public function validate(Tag $tag, array $attributes): array
    {
        return validator(
            $attributes,
            [
                'name' => [Rule::when($tag->exists, 'sometimes'), 'required', 'string']
            ]
        )->validate();
    }
}