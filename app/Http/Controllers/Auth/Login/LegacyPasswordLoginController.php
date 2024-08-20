<?php

namespace App\Http\Controllers\Auth\Login;

use App\Http\Controllers\Controller;
use App\Http\Requests\Validators\Auth\Login\PasswordLoginValidator;
use App\Models\User;
use App\Models\Users\{Admin, Buyer, Vendor};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LegacyPasswordLoginController extends Controller
{
    /**
     * Handle an admin's authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminAuthenticate()
    {
        $credentials = (new PasswordLoginValidator)->validate(request()->all());
        return $this->authenticate($credentials, Admin::class);
    }

    /**
     * Handle a client's authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function buyerAuthenticate()
    {
        $credentials = (new PasswordLoginValidator)->validate(request()->all());
        return $this->authenticate($credentials, Buyer::class);
    }

    /**
     * Handle a talent's authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function vendorAuthenticate()
    {
        $credentials = (new PasswordLoginValidator)->validate(request()->all());
        return $this->authenticate($credentials, Vendor::class);
    }

    public function authenticate(array $credentials, string $authenticable)
    {
        $authenticables = [
            'admin' => Admin::class,
            'buyer' => Buyer::class,
            'vendor' => Vendor::class
        ];
        $authenticable_method = array_search($authenticable, $authenticables);

        if(!$authenticable_method)
        {
            throw ValidationException::withMessages([
                'email' => 'Some error occured while trying to log in'
            ]);
        }

        $attempted_user = User::firstWhere('email', $credentials['email']);

        $attempts = (int)$attempted_user->login_attempts;
        $ip = request()->ip();
        
        if(!is_null($attempted_user))
        {
            $next_login = $attempted_user->login_again_at;

            if(!is_null($next_login) && $next_login > date(env('APP_TIME_FORMAT')))
            {
                return response(['message' => "You have made {$attempts} unsuccesful attempts to login. Wait for ". time_elapsed_string($next_login) . " or try using Forgot Password"], 429);
            }else if(!is_null($next_login) && $next_login < date(env('APP_TIME_FORMAT')))
            {
                $attempted_user->update(['login_attempts' => 0]);
                $attempts = 0;
            }
        }

        if (Auth::attempt([
            ...Arr::only($credentials, ['email', 'password']),
            fn($query) => $query->has($authenticable_method)
        ]))
        {
            $attempted_user = User::find(auth()->id());

            $authenticable_user = auth()->user()->$authenticable_method;

            $attempts++;
            logWithIP($ip, "Succesful login as {$authenticable_method} with account ". auth()->user()->email . " after {$attempts} times(s)");
            $attempted_user->update(['login_attempts' => 0]);
            
            Auth::logout();
            Auth::guard($authenticable_method)->login($authenticable_user, true);
        
            request()->session()->regenerate();
            return response(['message' => 'Login succesful']);
        }

        if(!$attempted_user)
        {
            $log_message = "Failed attempt to log in as {$authenticable_method}";
        }
        else{
            $attempts++;
            $attempted_user->fill(['login_attempts' => $attempts])->save();
            $log_message = ordinal($attempts) . " failed attempt(s) to log in as {$authenticable_method} with account {$attempted_user->email}";

            if($attempts >= (int)env('MAX_LOGIN_ATTEMPTS', 5))
            {
                $attempted_user->update(['login_again_at' => nextDate("+" . env('FAILED_LOGIN_WAIT_DURATION', 3600) . " seconds")]);
            }
        }
        Auth::logout();
        logWithIP($ip, $log_message);

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }
}
