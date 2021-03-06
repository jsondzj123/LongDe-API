<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request)  
        ->header('Access-Control-Max-Age', '1728000')
        ->header('Access-Control-Allow-Credentials', 'false')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Headers', 'Authorization')
        ->header('Access-Control-Expose-Headers', 'Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, X-Requested-By, Access-Control-Allow-Methods')
        ->header('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS');
    }
}
