<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Request;
use App\Services\MenuService;
use Event;
use App;
use Auth;
use DB;



class SetDefaults {



    public function handle(Request $request, Closure $next)
    {
        $role = 1000;
        if (Auth::check()) $role = Auth::user()->customertypeid;
        config(['user.role' => $role]);


        //$host is e.g. rentallaravel.consiglia.dk
        //if (session('config', -1) == 1)
        {
            $host = $request->getHost();
            //TODO: Fix this, gives error class sfConfig not found
            if ($host == 'xrentallaravel.consiglia.dk') $host = 'cangeroni.hasselbalch.com';
            $code = 1;
            $config = DB::table('config')->where('url', $host)->first();
            if ($config) {
                $code = $config->index;
            }
            if ($code != '1') {
                if (eval($code . "\nreturn 'OK';") != 'OK')
                    echo("Code not evaluated OK");
            }
            else session(['config' => 1]);
        }

        return $next($request);
    }

}

class sfConfig {
    public static function set($key, $value)
    {
        $key = str_replace('sf_', 'app.', $key);
        config([$key => $value]);
    }
}