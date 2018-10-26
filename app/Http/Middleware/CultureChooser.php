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

        //Save path history in session
        $sanitizedpath1back = session('sanitizedpath', '');
        $sanitizedpath2back = session('sanitizedpath1back', '');

        if(strpos($sanitizedpath, 'ajax') === false) session(['sanitizedpath' => $sanitizedpath]);
        session(['sanitizedpath1back' => $sanitizedpath1back]);
        session(['sanitizedpath2back' => $sanitizedpath2back]);

        session(['querystring1back' => session('querystring', '')]);
        if(strpos($sanitizedpath, 'ajax') === false) session(['querystring' => $querystring]);

        return $next($request);
    }
}