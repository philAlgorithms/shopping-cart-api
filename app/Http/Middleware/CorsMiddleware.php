<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, Origin, Credentials');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        $allowedOrigins = ['https://samandcart.test', 'https://samandcart.test', 'https://samandcart.com', 'https://test.samandcart.com', 'samandcart.com', 'test.samandcart.com'];
        $origin = array_key_exists('HTTP_ORIGIN', $_SERVER) ? $_SERVER['HTTP_ORIGIN'] : null;

        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', App::environment('local') ? 'https://samandcart.test' : $origin);
        }
        return $response;
    }
}
