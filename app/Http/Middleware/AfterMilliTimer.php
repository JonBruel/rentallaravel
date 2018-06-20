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
        return $response;
    }

}
