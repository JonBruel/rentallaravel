<?php

namespace App\Http\Middleware;

use Closure;

class BeforeMilliTimer
{
    public static $tstart = 0;

    public function getMicroTime()
    {
        list( $usec, $sec ) = explode( ' ', microtime() );
        return round(( (float) $usec * 1000 + (float) $sec * 1000), 2);
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        //\Session::flash('timer','before');
        AfterMilliTimer::$tstart = $this->getMicroTime();
        return $next($request);
    }

}
