<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 22-06-2018
 * Time: 12:56
 */

namespace App\Http\Middleware;
use Closure;
use App;

class CultureChooser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Get locale from query string
        $locale = $request->query('culture',session('culture','da_DK'));
        $request->setLocale($locale);
        App::setLocale($locale);
        session(['culture' => $locale]);
        unset($request['culture']);
        session(['uri' => $request->path()]);


        //Clean culture element away from querystring
        $querystring = $request->getQueryString();
        $querystring = str_replace('&culture='.$locale,'',$querystring);
        $querystring = str_replace('culture='.$locale.'&','',$querystring);
        session(['querystring' => $querystring]);
        $sanitizedpath = '/'.$request->path().'?'.$querystring;
        $sanitizedpath = str_replace('//', '/', $sanitizedpath);
        session(['sanitizedpath' => $sanitizedpath]);
        return $next($request);
    }
}