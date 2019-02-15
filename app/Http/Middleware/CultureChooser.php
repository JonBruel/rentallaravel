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

/**
 * The class handles the setting of the culture, which defaults to da_DK. In the common view
 * the user can change the culture, and the new value will be stored in the session. In addition
 * the class also keeps track of the request history and the query string used for the navigation
 * information in the top of the page - including a return link. Ajax requests are kept out
 * of the history here.
 *
 * Class CultureChooser
 * @package App\Http\Middleware
 */
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
        setlocale(LC_TIME, App::getLocale()); //Used by Carbon
        session(['culture' => $locale]);
        unset($request['culture']);
        session(['uri' => $request->path()]);


        //Clean culture element and menupoint element away from querystring
        $querystring = $request->getQueryString();
        $querystring = str_replace('&culture='.$locale,'',$querystring);
        $querystring = str_replace('culture='.$locale.'&','',$querystring);
        $querystring = preg_replace ('/menupoint=(\d+)/', '', $querystring);

        $sanitizedpath = '/'.$request->path();
        $sanitizedpath = str_replace('//', '/', $sanitizedpath);
        session(['sanitizedpath' => $sanitizedpath]);
        $pathhistory = session('pathhistory', []);

        $path = str_replace('?&', '?', $sanitizedpath.'?'.$querystring);

        if (strpos($querystring,"back=1") !== false) {
            array_pop($pathhistory);

            //Remove back=1 from query, as we are not going to use it anymore
            unset($request['back']);
        }
        else {
            //Save path history in session, but not for ajax calls and non-GET requests
            if (($request->isMethod('GET')) && (strpos($sanitizedpath, 'ajax') === false))
            {
                //We only add the path info to the history if it is different from the last
                $addpath = true;
                $historylength = sizeof($pathhistory) - 1;
                if ($historylength >= 0) {
                    $lastpath = $pathhistory[$historylength];
                    if ($lastpath == $path) $addpath = false;
                }
                if ($addpath) {
                    $pathhistory[] = $path;
                }
            }
        }

        $historylength = sizeof($pathhistory);
        $back = "";
        if ($historylength > 1) {
            $back = $pathhistory[$historylength - 2];
        }
        session(['back' => $back]);
        session(['pathhistory' => $pathhistory]);

        return $next($request);
    }
}
