<?php
namespace App\Services;
use Auth;
use Illuminate\Support\Facades\Response;
use App\Models\HouseI18n;
use App\Models\House;
use App;



/**
 * The MenuStructure services has several purposes. First of all it generates the menustructure
 * based on the /app/config/menu.json file. It supports several levels and parent<->child relation-
 * ships, allowing the children to be shown when the parent is clicked. The MenuStructure only
 * dispalys menus the user has right to accourding to his ROLE and the ROLES heirarchy. The menu
 * item show may also depend on the type (site, hub, maindatabase) of the machine this application
 * runs on, and this dependency is included in the class.
 *
 * The rendered menu is rendered as an array with menu names, numbers, CSS class information,
 * etc., but the html format and translation should be handled by the view. The The content
 * is only updated when the menu structure in meny.yml has been changed, we use md5 for
 * a fast check of changes. When there is no change, we reuse the previous MenuSructure instance
 * which is saved in the session.
 *
 * @category  Rentallaravel
 * @author    Jon Brüel
 */
class MenuService {

    private $fullMenu = [];
    public static $userMenu;
    private $keepinfo = [];
    private $houses = -1;

    /**
     * Construction of the MenuStructure.
     */
    public function __construct() {
        $this->fullMenu = config('menu.menustructure');
        $userMenu = $this->fullMenu;

        //Determine active information part for the house
        $defaultHouse = session('defaultHouse', config('app.default_house', -1));
        $this->keepinfo = session($defaultHouse.App::getLocale(), []);
        if (sizeof($this->keepinfo) == 0)
        {
            $keepinfo = [];
            $infofieldstocheck = ['route', 'carrental', 'conditions', 'plan', 'nature', 'sports', 'shopping', 'environment', 'weather'];
            $info = HouseI18n::where('id', $defaultHouse)->where('culture', App::getLocale())->first();
            if ($info)
            {
                foreach ($infofieldstocheck as $infofield)
                {
                    $keepinfo['home/showinfo/'.$infofield] = ($info->$infofield != '');
                }
            }
            session([$defaultHouse.App::getLocale() => $keepinfo]);
            $this->keepinfo = $keepinfo;
        }

        //Determine if home/listhouses should be shown
        $this->houses = session('houses', -1);
        if ($this->houses = -1) {
            $this->houses = House::filter()->count();
            session(['houses' => $this->houses]);
        }

        array_walk($userMenu, [$this,'menufilter']);
        foreach ($userMenu as $key => $value) if ($value == null) unset($userMenu[$key]);
        static::$userMenu = $userMenu;
    }


    //Contructs the menu based on the customertypeid, very simple
    //where lower values has the same plus more access than the higher values.
    private function menufilter(Array &$value, $key) {
        $deletekey = false;

        //Menu role 10000: Only show when not logged in
        //menu role 10001: Only show when logged in.

        //First handle when user authenticated
        if (Auth::check()) {
            if (Auth::user()->customertypeid > $value['role']) $deletekey = true;
            if ($value['role'] == 10000) $deletekey = true;
        }

        //The handle if annonymous user
        else {
            if ($value['role'] == 10000) $deletekey = false;
            if ($value['role'] == 10001) $deletekey = true;
            if ($value['role'] < 1000) $deletekey = true;
        }
        $value['strenght'] = false;
        $value['key'] = $key;
        if ($deletekey) $value = null;

        //For the deletion of information fields which have not been given a value
        if (array_key_exists($value['path'], $this->keepinfo))
        {
           if ($this->keepinfo[$value['path']] == false) $value = null;
        }

        if (($this->houses == 1) && $value['path'] == 'home/listhouses') $value = null;

    }


    /*
     * This function changes the css classes, used for experimenting with the menu
     * structure. The aim is the create a "nice collapsable menu".
     */
    private function changecss(Array &$value, $key) {
       //TODO Implement it.
    }

    /**
     * When the user click a menu item, the request will be intercepted by the
     * Middleware, which will
     * call this method, which will set the appropriate variables and render
     * the menu. The rendered menu will be stores in a sessions variable, menu_rendered,
     * which will be used in the view for the generation of the html code.
     *
     * @param type $menuclicked, the menuitem number clicked
     */
    public function setClicked($menuclicked) {

        $level = 0;

        $userMenu = static::$userMenu;
        foreach ($userMenu as $menupoint => $entry) if (strpos($entry['path'],'?') === false) $userMenu[$menupoint]['path'] .= "?menupoint=". $menupoint;

        if (!array_key_exists($menuclicked, $userMenu)) $menuclicked =  0;
        if ($menuclicked > 0) {

            //We hide all menues at level > 1
            foreach ($userMenu as $menupoint => $entry) {
                    if (($userMenu[$menupoint]['level'] > 1) && ($userMenu[$menupoint]['show'] != 'select')) {
                        $userMenu[$menupoint]['show'] = 'hide';
                    }
            }

            $level = $userMenu[$menuclicked]['level'];
            $presentnum = $menuclicked;

            //The chosen is always show
            $userMenu[$presentnum]['show'] = 'show';


            //We show the structure below
            foreach ($userMenu[$presentnum]['childrenmap'] as $child => $value) {
                if (!array_key_exists($child, $userMenu)) continue;
                if ($userMenu[$child]['show'] != 'select') {
                    $userMenu[$child]['show'] = 'show';
                }
            }

            //We move up in the structure from this level to the lower levels
            while ($level >= 1) {
                $userMenu[$presentnum]['cssclass'] = "menulevel" . $level . "chosen";
                $userMenu[$presentnum]['strenght'] = true;

                //We set the siblings to show  $userMenu[$parentid]['childrenmap']
                foreach ($userMenu[$userMenu[$presentnum]['parentid']]['childrenmap'] as $child) {
                    if (!array_key_exists($child, $userMenu)) continue;
                    if ($userMenu[$child]['show'] != 'select') {
                        $userMenu[$child]['show'] = 'show';
                    }
                }
                $level--;
                $presentnum = $userMenu[$presentnum]['parentid'];
            }
        }
        static::$userMenu = $userMenu;
        return $userMenu;
    }

}