<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class LogoutController extends Controller
{
    public function __invoke()
    {
        // if (EnsureFrontendRequestsAreStateful::fromFrontend(request())) {
        Auth::logout();
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        Auth::guard('buyer')->logout();
        Auth::guard('vendor')->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();
    }
}
