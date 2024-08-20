<?php
namespace App\Http\Requests\Validators;

use App\Models\Advert;
use Illuminate\Validation\Rule;

class AdvertValidator
{
    public function validate(Advert $advert, array $attributes): array
    {
        return validator(
            $attributes,
            [
                'heading' => [Rule::when($advert->exists, 'sometimes'), 'required', 'string'],
                'link' => [Rule::when($advert->exists, 'sometimes'), 'required', 'string', 'active_url'],
                'description' => [Rule::when($advert->exists, 'sometimes'), 'required', 'string'],
                'image' => [Rule::when($advert->exists, 'sometimes'), 'required', 'image', 'mimes:png,jpg,jpeg'],
            ]
        )->validate();
    }
}