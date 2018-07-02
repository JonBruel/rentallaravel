<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Http\Request;
use App\Services\MenuService;
use Event;



class MenupointGetter {



    public function handle(Request $request, Closure $next)
    {
        //Get menupoint from query string
        $menupoint = $request->query('menupoint',session('menupoint',0));

        //The following 5 lines control where to go when we start impersonating
        $manager = app('impersonate');
        if ($manager->isImpersonating()) {
            if (session('impersonate', false) == false) $menupoint = 2110;
            session(['impersonate' => true]);
        }
        else session(['impersonate' => false]);;

        session(['menupoint' => $menupoint]);

        $menuService = new MenuService();
        session(['menuStructure' => $menuService->setClicked($menupoint)]);

        return $next($request);
    }

}
