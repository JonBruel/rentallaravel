<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Request;


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
    public function handle(Request $request, Closure $next)
    {

        AfterMilliTimer::$tstart = $this->getMicroTime();
        return $next($request);
    }

}
