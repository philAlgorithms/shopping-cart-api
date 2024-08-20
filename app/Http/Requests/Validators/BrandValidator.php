<?php
namespace App\Http\Requests\Validators;

use App\Models\Media\{MediaFile};
use App\Models\Products\{Brand};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class BrandValidator
{
    public function validate(Brand $brand, Request $request):array
    {
        $attributes = $request->all();
        return validator(
            $attributes,
            [
                'name' => [Rule::when($brand->exists, 'sometimes'), 'required', 'string'],
                'logo' => [Rule::when($brand->exists, 'sometimes'), 'required', 'image', 'mimes:png,jpg,jpeg,svg', 'max:5000'],
                // 'logo_id' => [Rule::when($brand->exists, 'sometimes'), 'required_without:logo', Rule::exists('media_files', 'id')],
                'tags' => ['sometimes', 'array'],
                'tags.*' => ['integer', Rule::exists('tags', 'id')],
            ]
        )->validate();
    }

    public function validateAndPrepare(Brand $brand, Request $request): array
    {
        $validated = $this->validate($brand, $request);

        // For performance purposes, saving an existing file takes precedence over saving an uploaded file
        if(array_key_exists('logo', $validated) && !array_key_exists('logo_id', $validated))
        {
            $uploaded_logo = request()->file('logo');
            $logo = fillMediaFile(uploaded_file: $uploaded_logo, disk: env('DEFAULT_DISK', 'local'));
            $validated['uploaded_logo'] = $logo;
        }

        $prepared = Arr::except($validated, ['logo']);

        return $prepared;
    }
}