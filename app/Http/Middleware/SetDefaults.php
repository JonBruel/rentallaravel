<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Request;
use App\Services\MenuService;
use Event;
use App;
use Auth;
use DB;
use App\Models\HouseI18n;



class SetDefaults {

    public function getMicroTime()
    {
        list( $usec, $sec ) = explode( ' ', microtime() );
        return round(( (float) $usec * 1000 + (float) $sec * 1000), 2);
    }

    public function handle(Request $request, Closure $next)
    {
        AfterMilliTimer::$tstart = $this->getMicroTime();
        $role = 1000;
        $ownerid = -1;
        $_SESSION['user'] = null;
        if (Auth::check())
        {
            $role = Auth::user()->customertypeid;
            $ownerid = ($role > 10)?Auth::user()->ownerid:Auth::user()->id;

            //The SESSION is used for the tinymce MyPlugin authentication functions
            $userarray = Auth::user()->toArray();
            $userarray['general.language'] = substr(Auth::user()->culture->culture,0,2);
            $_SESSION['user'] = $userarray;
            session(['customerid' => Auth::user()->id]);
            //die($_SESSION['user']['name']);
            config(['user.role' => $role]);
            config(['user.ownerid' => $ownerid]);
            session(['customerid' => Auth::user()->id]);
        }


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

        $culture = App::getLocale();
        session(['keywords' => '']);
        session(['description' => '']);

        //Store houseinformation in the session
        if (session('defaultHouse', -1) != -1)
        {
            $defaultHouse = session('defaultHouse');
            if (!session($defaultHouse.'housedescriptions'))
            {
                $housedescriptions = [];
                foreach(HouseI18n::where('id', $defaultHouse)->get() as $des) $housedescriptions[$des->culture] = $des;
                session([$defaultHouse.'housedescriptions' => $housedescriptions]);
            }
        }

        //Store information used for meta tags in the session
        $housedescriptions = session($defaultHouse.'housedescriptions');
        if (array_key_exists($culture, $housedescriptions))
        {
            session(['keywords' => $housedescriptions[$culture]->keywords]);
            $descriptions = explode('|', $housedescriptions[$culture]->seo);
            $ran = random_int(0, sizeof($descriptions)-1);
            session(['description' => $descriptions[$ran]]);
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
