<?php

namespace App\Http\Controllers\Auth\Login;

use App\Http\Controllers\Controller;
use App\Http\Requests\Validators\Auth\Login\PasswordLoginValidator;
use App\Http\Resources\{UserResource};
use App\Http\Resources\Users\{AdminResource, BuyerResource, LogisticsPersonnelResource, VendorResource};
use App\Models\{User};
use App\Models\Users\{Admin, Buyer, LogisticsPersonnel, Vendor};
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class PasswordLoginController extends Controller
{
    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), env('MAX_LOGIN_ATTEMPTS', 5))) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower(request()->input('email')) . '|' . request()->ip());
    }
    /**
     * Handle an admin's authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminAuthenticate()
    {
        $credentials = (new PasswordLoginValidator)->validate(request()->all());
        return $this->authenticate($credentials, 'admin');
    }

    /**
     * Handle a buyer's authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buyerAuthenticate()
    {
        $credentials = (new PasswordLoginValidator)->validate(request()->all());
        return $this->authenticate($credentials, 'buyer');
    }

    /**
     * Handle a vendor's authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function vendorAuthenticate()
    {
        $credentials = (new PasswordLoginValidator)->validate(request()->all());
        return $this->authenticate($credentials, 'vendor');
    }

    /**
     * Handle general authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generalAuthenticate()
    {
        $credentials = (new PasswordLoginValidator)->validate(request()->all());
        try {
            return $this->authenticate($credentials, 'buyer');
        } catch (ValidationException $e) {
            try {
                return $this->authenticate($credentials, 'vendor');
            } catch (ValidationException $e) {
                try {
                    return $this->authenticate($credentials, 'logistics_personnel');
                } catch (ValidationException $e) {
                    return $this->authenticate($credentials, 'admin');
                }
            }
        }
    }

    public function authenticateLegacy(array $credentials, string $authenticable)
    {
        $authenticables = [
            'admin' => Admin::class,
            'buyer' => Buyer::class,
            'vendor' => Vendor::class
        ];
        $authenticable_method = array_search($authenticable, $authenticables);

        if (!$authenticable_method) {
            throw ValidationException::withMessages([
                'email' => 'Some error occured while trying to log in'
            ]);
        }

        $attempted_user = User::firstWhere('email', $credentials['email']);

        $attempts = !$attempted_user ? 0 : (int)$attempted_user->login_attempts;
        $ip = request()->ip();

        if (!is_null($attempted_user)) {
            $next_login = $attempted_user->login_again_at;

            if (!is_null($next_login) && $next_login > date(env('APP_TIME_FORMAT'))) {
                return response(['message' => "You have made {$attempts} unsuccesful attempts to login. Wait for " . time_elapsed_string($next_login) . " or try using Forgot Password"], 429);
            } else if (!is_null($next_login) && $next_login < date(env('APP_TIME_FORMAT'))) {
                $attempted_user->update(['login_attempts' => 0]);
                $attempts = 0;
            }
        }

        if (Auth::attempt([
            ...Arr::only($credentials, ['email', 'password']),
            fn ($query) => $query->has($authenticable_method)
        ])) {
            $attempted_user = User::find(auth()->id());

            $authenticable_user = auth()->user()->$authenticable_method;

            $attempts++;
            logWithIP($ip, "Succesful login as {$authenticable_method} with account " . auth()->user()->email . " after {$attempts} times(s)");
            $attempted_user->update(['login_attempts' => 0]);

            // Logout all probable authenticables
            Auth::logout();
            Auth::guard('admin')->logout();
            Auth::guard('buyer')->logout();
            Auth::guard('vendor')->logout();

            // Signin attempted user
            Auth::guard($authenticable_method)->login($authenticable_user, true);

            request()->session()->regenerate();
            return response(['message' => 'Login succesful']);
        }

        if (!$attempted_user) {
            $log_message = "Failed attempt to log in as {$authenticable_method}";
        } else {
            $attempts++;
            $attempted_user->fill(['login_attempts' => $attempts])->save();
            $log_message = ordinal($attempts) . " failed attempt(s) to log in as {$authenticable_method} with account {$attempted_user->email}";

            if ($attempts >= (int)env('MAX_LOGIN_ATTEMPTS', 5)) {
                $attempted_user->update(['login_again_at' => nextDate("+" . env('FAILED_LOGIN_WAIT_DURATION', 3600) . " seconds")]);
            }
        }
        Auth::logout();
        logWithIP($ip, $log_message);

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }


    public function authenticateLegacyV2(array $credentials, string $authenticable)
    {
        $authenticables = [
            'admin' => Admin::class,
            'buyer' => Buyer::class,
            'vendor' => Vendor::class
        ];
        $authenticable_method = array_search($authenticable, $authenticables);

        if (!$authenticable_method) {
            throw ValidationException::withMessages([
                'email' => 'Some error occured while trying to log in'
            ]);
        }

        $this->ensureIsNotRateLimited();

        if (
            Auth::once(request()->only('email', 'password')) &&
            !is_null($authenticable_user = auth()->user()->$authenticable_method)
        ) {
            // Logout all probable authenticables
            Auth::logout();
            // foreach($authenticables as $guard => $instance)
            // {
            //     Auth::guard($guard)->logout();
            // }
            // request()->session()->invalidate();
            // request()->session()->regenerateToken();

            Auth::guard($authenticable_method)->login($authenticable_user, request()->boolean('remember'));

            request()->session()->regenerate();

            return response(['message' => 'Login succesful']);
        } else {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function authenticate(array $credentials, string $guard)
    {
        $remember = array_key_exists('remember', $credentials) ? $credentials['remember'] : false;
        $this->ensureIsNotRateLimited();
        if (Auth::guard($guard)->once(
            Arr::only($credentials, ['email', 'password'])
        )) {
            // Auth::logout();
            // Auth::guard('web')->logout();
            // Auth::guard('admin')->logout();
            // Auth::guard('buyer')->logout();
            // Auth::guard('vendor')->logout();
    
            request()->session()->invalidate();
    
            request()->session()->regenerateToken();
            Auth::guard($guard)->attempt(
                Arr::only($credentials, ['email', 'password']),
                $remember
            );
            request()->session()->regenerate();

            switch ($guard) {
                case 'buyer':
                    return BuyerResource::make(Buyer::firstWhere('email', $credentials['email']));
                    break;
                case 'vendor':
                    return VendorResource::make(Vendor::firstWhere('email', $credentials['email']));
                    break;
                case 'logistics_personnel':
                    return LogisticsPersonnelResource::make(LogisticsPersonnel::firstWhere('email', $credentials['email']));
                    break;
                case 'admin':
                    return AdminResource::make(Admin::firstWhere('email', $credentials['email']));
                    break;
                default:
                    return auth()->user();
            };
        } else {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }
    
    public function authenticateNew(array $credentials, string $authenticable)
    {
        $authenticables = [
            'admin' => Admin::class,
            'buyer' => Buyer::class,
            'vendor' => Vendor::class,
            'logistics_personnel' => LogisticsPersonnel::class
        ];
        $authenticable_method = array_search($authenticable, $authenticables);

        if (!$authenticable_method) {
            throw ValidationException::withMessages([
                'email' => 'Some error occured while trying to log in'
            ]);
        }

        $this->ensureIsNotRateLimited();

        if (
            Auth::once(request()->only('email', 'password')) &&
            !is_null($authenticable_user = auth()->user()->$authenticable_method)
        ) {
            // Logout all probable authenticables
            Auth::guard('web')->logout();
            foreach ($authenticables as $guard => $instance) {
                Auth::guard($guard)->logout();
            }
            /**
             * Study more about these
             */
            // request()->session()->invalidate();
            // request()->session()->regenerateToken();

            Auth::guard($authenticable_method)->login($authenticable_user, request()->boolean('remember'));

            request()->session()->regenerate();
            $user = User::firstWhere('email', $credentials['email']);

            return UserResource::make($user);
        } else {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }
}
