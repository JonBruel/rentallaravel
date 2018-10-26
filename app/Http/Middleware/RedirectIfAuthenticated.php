<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class RedirectIfAuthenticated controls the behaviour after a customer has authenticated. The
 * package has been changed from the original Laravel version.
 *
 * @package App\Http\Middleware
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //Changes made from the original Laravel version to handle the flow after
        //a new customer has registered.
        if ((Auth::guard($guard)->check()) && (auth()->user()->verified == 1) && (session('isRedirected') != 1)) {
            Log::notice('Redirecting to \home in RedirectIfAuthenticated middleware.');
            return redirect('/home');
        }
        Log::notice('NOT redirecting to \home in RedirectIfAuthenticated middleware.');
        return $next($request);
    }
}
