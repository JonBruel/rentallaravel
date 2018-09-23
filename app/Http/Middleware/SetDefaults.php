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
        $ownerid = -1;
        if (Auth::check())
        {
            $role = Auth::user()->customertypeid;
            $ownerid = Auth::user()->ownerid;
        }
        config(['user.role' => $role]);
        config(['user.ownerid' => $ownerid]);

        //$host is e.g. rentallaravel.consiglia.dk
        //if (session('config', -1) == 1)
        {
            $url = $request->getHost();
            //TODO: Fix this, gives error class sfConfig not found
            if ($url == 'rentallaravel.consiglia.dk') $url = 'cangeroni.hasselbalch.com';
            $code = 1;
            $config = DB::table('config')->where('url', $url)->first();
            if ($config) {
                $code = $config->index;
                $code = str_replace('sfConfig::', '$this->', $code);
            }
            if ($code != '1') {
                if (eval($code . "\nreturn 'OK';") != 'OK')
                    echo("Code not evaluated OK");
            }
            else session(['config' => 1]);
        }

        config(['app.host' => 'rentallaravel.consiglia.dk']);
        session(['host' => 'rentallaravel.consiglia.dk']);

        return $next($request);
    }

    public static function set($key, $value)
    {
        $key = str_replace('sf_', 'app.', $key);
        config([$key => $value]);
    }

}
