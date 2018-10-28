<?php

namespace App\Http\Middleware;

use Closure;


/**
 * Class AfterMilliTimer has the main purpose of measuring the time in milliseconds
 * from starting the first middleware to leaving for the rendering of the view. It also sets
 * some headers for the response.
 *
 * @todo Check if the headers are required.
 *
 * @package App\Http\Middleware
 */
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

        //$response->header('X-Through-Controller','Yes'); //For test

        //I don't remember if these lines are used at all.
        $response->header("pragma", "no-cache");
        $response->header("Cache-Control", "no-store,no-cache, must-revalidate, post-check=0, pre-check=0");

        return $response;
    }

}
