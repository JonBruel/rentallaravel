<?php
namespace App\Services;
use Auth;
use App\Models\HouseI18n;
use App\Models\House;
use App;



/**
 * The MenuStructure services has several purposes. First of all it generates the menu structure
 * based on the /app/config/menu.json file. It supports several levels and parent<->child relation-
 * ships, allowing the children to be shown when the parent is clicked. The MenuStructure only
 * dispalys menus the user has right to according to his customertypeid.
 *
 * The rendered menu is rendered as an array with menu names, numbers, CSS class information,
 * etc., but the html format and translation should be handled by the view. The content
 * which is based on the configuration file menu.cfg is checked everytime. We could cash the information,
 * but the speed is very high and we would only save around 1 ms by doing so.
 *
 * @category  Rentallaravel
 * @author    Jon Brüel
 */
class MenuService {

    /**
     * @var array is the unfiltered menu from the menu.cfg file.
     */
    private $fullMenu = [];

    /**
     * @var array $userMenu is created from the full menu but only includes the menu points available for the user according to his rights, etc.
     */
    public static $userMenu;

    /**
     * @var array $keepinfo hold information about the menu points which we want to omit because they are about information not filled in in the table house_i18n.
     */
    private $keepinfo = [];

    /**
     * @var int $houses will be given the number of houses existing under the realm of the site.
     */
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


    /**
     * Contructs the menu based on the customertypeid, using a rather simple method
     * where (in general) lower values have the same and more rights than higher values.
     *
     * @param array $value
     * @param int $key being the menupoint
     */
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

        //Hide home/listhouses menu point if there only is one house
        if (($this->houses == 1) && $value['path'] == 'home/listhouses') $value = null;
    }


    /**
     * Not implemented
     *
     * @param array $value
     * @param int $key
     */
    private function changecss(Array &$value, $key) {
       //TODO Implement it.
    }


    /**
     * When the user clicks a menu item, the request will be intercepted by the Middleware,
     * MenupointGetter, which calls this method at every request. The returned value will in the
     * veiw be rendered as the menu.
     *
     * @param int $menuclicked
     * @return array with the menu structure where the clicked menupoint and its parrent and children has been marked in order to show it differently
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
