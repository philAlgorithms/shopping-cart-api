<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\{User};
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateuserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $user = auth()->user()->user;
        
        $validated = validator(
            request()->all(),
            [
                'first_name' => [Rule::when($user->exists, 'sometimes'), 'required', 'string'],
                'last_name' => [Rule::when($user->exists, 'sometimes'), 'required', 'string'],
                'address' => [Rule::when($user->exists, 'sometimes'), 'required', 'string'],
                'city' => [Rule::when($user->exists, 'sometimes'), 'required', 'string'],
                'avatar' => [Rule::when($user->exists, 'sometimes'), 'required', 'image', 'mimes:png,jpg,jpeg'],
            ]
        )->validate();

        $user = DB::transaction(function () use ($user, $validated) {
            $prepared = Arr::except($validated, ['avatar']);
    
            if(array_key_exists('avatar', $validated))
            {
                $uploaded_avatar = request()->file('avatar');
                $avatar = saveOrUpdateMediaFile(
                    media_file: $user->coverImage,
                    uploaded_file: $uploaded_avatar, 
                    disk: env('DEFAULT_DISK', 'local'), 
                    path: 'projects/portfolio',
                    delete_media: true,
                    callback: function($model) use($prepared) {
                        // $model->save();
                    }
                );
                $prepared['avatar_id'] = $avatar->id;
            }

            $user->fill($prepared);
            $user->save();

            return $user;
        });

        return UserResource::make(
            $user
        );
    }

    public function uploadBvn()
    {
        $this->authorize('uploadBvn', [User::class]);
        $user = auth()->user()->user;
        
        $validated = validator(
            request()->all(),
            [
                'bvn' => ['required', 'string','numeric', 'regex:/^[0-9]{11}$/'],
                'bank_account_number' => ['required', 'string','numeric', 'regex:/^[0-9]{10}$/'],
                'bank_id' => ['required', 'numeric', Rule::exists('banks', 'id')],
            ],
            [
                'bvn.regex' => 'The bvn you uploaded is invalid',
                'bank_account_number.regex' => 'The account number you uploaded is invalid',
                'bank_id.exists' => 'The selected bank is invalid',
            ]
        )->validate();

        $user->updateBvn($validated);

        return UserResource::make(
            $user->refresh()
        );
    }

    public function verifyBvn(User $user)
    {
        $this->authorize('verifyBvn', $user);

        $user->verifyBvn();

        return UserResource::make(
            $user->refresh()
        );
    }

    public function declineBvn(User $user)
    {
        $this->authorize('declineBvn', $user);

        $user->declineBvn();

        return UserResource::make(
            $user->refresh()
        );
    }
}
