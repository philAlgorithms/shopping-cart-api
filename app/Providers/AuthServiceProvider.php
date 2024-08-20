<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Order\{Journey, OrderJourney};
use App\Policies\{JourneyPolicy, OrderJourneyPolicy};
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        OrderJourney::class => OrderJourneyPolicy::class,
        Journey::class => JourneyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function (Authenticatable $user, string $token) {
            return config('app.rest_passsword_url', env('RESET_PASSWORD_URL', 'https://samandcart.com/reset-password')) . '?token=' . $token;
        });
    }
}
