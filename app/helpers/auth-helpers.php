<?php

use App\Models\User;
use App\Models\Users\{Admin, Buyer, LogisticsPersonnel, Vendor};
use Illuminate\Contracts\Auth\Authenticatable;

function isAdmin(Authenticatable $user)
{
    return $user instanceof Admin;
}

function isVendor(Authenticatable $user)
{
    return $user instanceof Vendor;
}

function isBuyer(Authenticatable $user)
{
    return $user instanceof Buyer;
}

function getGuard(string $value, ?string $key = 'email'): string|null
{
    return Admin::firstWhere($key, $value) ? 'admin' : (
        Vendor::firstWhere($key, $value) ? 'vendor' : (
            LogisticsPersonnel::firstWhere($key, $value) ? 'logistics_personnel' : (Buyer::firstWhere($key, $value) ? 'buyer' : null)
        )
    );
}

function getBroker(string $value, ?string $key = 'email'): string|null
{
    if($guard = getGuard($value, $key))
        return $guard . 's';
    return null;
}