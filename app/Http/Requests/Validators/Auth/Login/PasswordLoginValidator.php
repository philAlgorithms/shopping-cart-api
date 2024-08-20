<?php

namespace App\Http\Requests\Validators\Auth\Login;

use App\Models\{Currency, TimeUnit};
use App\Models\Projects\{ProjectProposal, ProjectType};
use App\Models\Skills\{Discipline};
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PasswordLoginValidator
{
    public function validate(array $attributes): array
    {
        $validated = validator(
            $attributes, [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean']
        ])->validate();

        $validated['remember'] = array_key_exists('remember', $validated) ? (bool)$validated['remember'] : false;

        return $validated;
    }
}