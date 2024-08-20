<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\{AdminResource, BuyerResource, VendorResource};
use App\Models\{BuyerReferralProgram, User};
use App\Models\Stores\Store;
use App\Models\Users\Admin;
use App\Models\Users\LogisticsPersonnel;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    /**
     * Register a buyer
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function registerBuyer()
    {
        $validated = validator(
            request()->all(),
            [
                'email' => ['required', 'email', 'unique:buyers,email'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'regex:' . env('PHONE_NUMBER_PATTERN')],
                'password' => ['required', 'confirmed', 'regex:' . env('PASSWORD_PATTERN')],
                'referral_code' => ['sometimes', 'string', Rule::exists('buyer_referral_programs', 'code')],
            ],
            [
                'phone_number.regex' => 'This is not a valid Nigerian phone number',
                'password.regex' => 'Password is not strong enough.'
            ]
        )->validate();

        $program = array_key_exists('referral_code', $validated) ? BuyerReferralProgram::firstWhere('code', $validated['referral_code']) : null;
        if(! is_null($program) && $program->is_deactivated)
        {
            throw ValidationException::withMessages([
                'referral' => 'Referral code is deactivated'
            ]);
        }

        $user = (new User)->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone_number' => $validated['phone_number'] 
        ]);

        $buyer = DB::transaction(function () use ($user, $validated) {
            $user->save();
            $buyer = $user->buyer()->create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password'])
            ]);

            return $buyer;
        });

        event(new Registered($buyer));
        Auth::guard('buyer')->login($buyer, true);

        if(! is_null($program))
        {
            $program->addReferral($buyer);
        }
        return BuyerResource::make(
            $buyer->load(['user'])
        );
    }

    /**
     * Register a vendor
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function registerVendor(): JsonResource
    {
        $validated = validator(
            request()->all(),
            [
                'email' => ['required', 'email', 'unique:vendors,email'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'regex:' . env('PHONE_NUMBER_PATTERN')],
                'password' => ['required', 'confirmed', 'regex:' . env('PASSWORD_PATTERN')],
                'store_name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'max:4096'],
                'store_url' => ['required', 'string', 'max:255', Rule::unique('stores', 'key')],
                'town_id' => ['required', 'integer', Rule::exists('towns', 'id')],
                'address' => ['required', 'string', 'max:255'],
            ],
            [
                'phone_number.regex' => 'This is not a valid Nigerian phone number',
                'password.regex' => 'Password is not strong enough.'
            ]
        )->validate();

        $user = (new User)->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
        ]);

        $vendor = DB::transaction(function () use ($user, $validated) {
            $user->save();
            $vendor = $user->vendor()->create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'town_id' => $validated['town_id']
            ]);

            Store::create([
                'key' => $validated['store_url'],
                'name' => $validated['store_name'],
                'description' => $validated['description'],
                'vendor_id' => $vendor->id
            ]);

            return $vendor;
        });

        event(new Registered($vendor));
        Auth::guard('vendor')->login($vendor, true);

        return VendorResource::make(
            $vendor->load(['user'])
        );
    }
    /**
     * Register a admin
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function registerAdmin(): JsonResource
    {
        $this->authorize('create', [Admin::class]);
        $validated = validator(
            request()->all(),
            [
                'email' => ['required', 'email', 'unique:admins,email'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'regex:' . env('PHONE_NUMBER_PATTERN')],
                'password' => ['required', 'confirmed', 'regex:' . env('PASSWORD_PATTERN')],
            ],
            [
                'phone_number.regex' => 'This is not a valid Nigerian phone number',
                'password.regex' => 'Password is not strong enough.'
            ]
        )->validate();

        $user = (new User)->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone_number' => $validated['phone_number'] 
        ]);

        $admin = DB::transaction(function () use ($user, $validated) {
            $user->save();
            $admin = $user->admin()->create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password'])
            ]);

            return $admin;
        });

        Auth::guard('admin')->login($admin, true);


        return AdminResource::make(
            $admin->load(['user'])
        );
    }
    
    /**
     * Register a admin
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function registerLogisticsPersonnel(): JsonResource
    {
        $this->authorize('create', [LogisticsPersonnel::class]);
        $validated = validator(
            request()->all(),
            [
                'email' => ['required', 'email', 'unique:logistics_personnels,email'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'regex:' . env('PHONE_NUMBER_PATTERN')],
                'password' => ['required', 'confirmed', 'regex:' . env('PASSWORD_PATTERN')],
            ],
            [
                'phone_number.regex' => 'This is not a valid Nigerian phone number',
                'password.regex' => 'Password is not strong enough.'
            ]
        )->validate();

        $user = (new User)->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone_number' => $validated['phone_number'] 
        ]);

        $logisticsPersonnel = DB::transaction(function () use ($user, $validated) {
            $user->save();
            $logisticsPersonnel = $user->logisticsPersonnel()->create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password'])
            ]);

            return $logisticsPersonnel;
        });


        return AdminResource::make(
            $logisticsPersonnel->load(['user'])
        );
    }
}
