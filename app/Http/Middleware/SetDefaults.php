<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Request;
use App\Services\MenuService;
use Event;
use App;
use Auth;



class SetDefaults {



    public function handle(Request $request, Closure $next)
    {
        $role = 1000;
        if (Auth::check()) $role = Auth::user()->customertypeid;
        config(['user.role' => $role]);
        return $next($request);
    }

}
