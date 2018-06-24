<?php

namespace App\Http\Middleware;

use Closure;

class AfterMilliTimer
{


    public static $tstart = 0;

    public function getMicroTime()
    {
        list( $usec, $sec ) = explode( ' ', microtime() );
        return round(( (float) $usec * 1000 + (float) $sec * 1000), 2);
    }


    public function handle($request, Closure $next)
    {
        $response = $next($request);

        self::$tstart = $this->getMicroTime() - self::$tstart;
        $lapse = 'Timelapse  in milliseconds: ' . round(self::$tstart , 2);
        //$request->getSession()->flash('timer',$lapse);
        \Session::flash('timer',$lapse);
        \Session::put('ost',$lapse);
        //$response = $response instanceof RedirectResponse ? $response : response($response);

        /*
        * Custom cache headers for js and css files, disbales with "x"
        */
        //}
        $response = $response->header('X-Through-Controller','Yes');
        $response->header("pragma", "no-cache");
        $response->header("Cache-Control", "no-store,no-cache, must-revalidate, post-check=0, pre-check=0");


        return $response;
    }

}
