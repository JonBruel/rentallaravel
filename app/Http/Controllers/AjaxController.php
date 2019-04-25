<?php
/**
* Created by PhpStorm.
* User: jbr
* Date: 20-10-2018
* Time: 17:05
*/


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Schema;
use ValidationAttributes;
use App\Models\BaseModel;
use App\Models\House;
use App\Models\HouseI18n;
use App\Models\Periodcontract;
use App\Models\Contract;
use Illuminate\Support\Facades\Input;
use Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;
use App;

/**
 * Class AjaxController is used for a limited set of ajax functions.
 *
 * For authentication we use the existing cookie based authentication used the the
 * non-ajax. In the ajax functions we set BaseModel::$ajax = true to avoid decimal
 * field to be changed to i18n strings.
 *
 * @package App\Http\Controllers
 */
class AjaxController extends Controller
{

    public function __construct() {
        parent::__construct(\App\Models\House::class);
        //We turn off mutators, we want . as decimal separator!
        BaseModel::$ajax = true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function listhouses($x1 = -179.99, $y1 = -179.99, $x2 = 179.99, $y2 = 179.99)
    {
        //TODO: modify from Symfony to Laravel

        $username = 'Not logged in';
        if (Auth::check())  $username = Auth::user()->name;

        Log::notice("User ".$username." has made an ajax request to listhouses.");

        $defaultHouse = Input::query('defaultHouse',config('app.default_house', -1));


        $housequery = House::whereBetween('latitude', [$x1, $x2])
                            ->whereBetween('longitude', [$y1, $y2]);

        if ($defaultHouse != -1) $housequery->where('id', $defaultHouse);

        if (session('housequery')) $housequery = session('housequery');
        $housequery->where('id', '>', 0);

        $houses = $housequery->get();


        $housefields = [];
        $i = 1;
        $housefields[0] =  ['length' => $i];

        if ($defaultHouse == -2)
        {
            //We have a new house not yet given coordinates
            $house = new House;
            $house->latitude = 0;
            $house->longitude = 0;
            $house->id = -2;
            $house->name = 'New House';
            $houses = [$house];
        }

        foreach ($houses as $house)
        {
            $latitude = $house->latitude;
            $longitude = $house->longitude;

            //Below we center the marker in case of a new house or an existing house without coordinate
            if (($latitude == 0) or ($latitude == null)) $latitude = ($x1+$x2)/2;
            if (($longitude == 0) or ($longitude == null)) $longitude = ($y1+$y2)/2;

            $housefields[$i] = $house->toArray();
            $housefields[$i]['latitude'] = $latitude;
            $housefields[$i]['longitude'] = $longitude;

            $housefields[$i]['veryshortdescription'] = HouseI18n::where('id', $house->id)->where('culture', App::getLocale())->first()->veryshortdescription;
            if ($i == 1)
            {
                $x1 = $latitude;
                $y1 = $longitude;
                $x2 = $latitude;
                $y2 = $longitude;
            }
            $x1 = min($x1, $latitude);
            $y1 = min($y1, $longitude);
            $x2 = max($x2, $latitude);
            $y2 = max($y2, $longitude);
            $i++;
        }

        $deltax = $x2 - $x1;
        $deltay = $y2 - $y1;
        $border = 0.2;
        $x1 = $x1 - $deltax*$border;
        $y1 = $y1 - $deltay*$border;
        $x2 = $x2 + $deltax*$border;
        $y2 = $y2 + $deltay*$border;

        $x1 = max(-179.99, $x1);
        $y1 = max(-179.99, $y1);
        $x2 = min(179.99, $x2);
        $y2 = min(179.99, $y2);
        $housefields[0]['length'] = $i;
        $housefields[0]['x1'] = $x1;
        $housefields[0]['y1'] = $y1;
        $housefields[0]['x2'] = $x2;
        $housefields[0]['y2'] = $y2;
        $coordinates = json_encode($housefields);

        return response()->json($housefields)
            ->header('Cache-Control', 'no-cache, must-revalidate');
    }

    public function getImportStatus()
    {
        $filename = base_path().'/storage/logs/migration.txt';
        $contents['text'] = str_replace("\n", '<br />',file_get_contents($filename));
        return response()->json($contents);
    }


    public function getGdprDeleteStatus()
    {
        $filename = base_path().'/storage/logs/gdprdelete.txt';
        $contents['text'] = str_replace("\n", '<br />',file_get_contents($filename));
        return response()->json($contents);
    }

    /*
     * This method is used in views where the user has the option
     * of scrolling through months and find some with vacancies and
     * within the month chosen by the user.
     * The following
    */
    public function getmonths($houseid)
    {
        // Takes around 120 ms, a clean SQL might be faster
        // $counts = Periodcontract::all()->where('houseid', $houseid)->where('from', '>', Carbon::now())
        //                             ->groupBy(function ($record) {return $record->from->format('Y-m');})
        //                             ->map(function ($month) { return $month->where('committed', null)->count();});

        //This one takes 45 ms.
        $months = DB::select(DB::raw("SELECT SUM(ISNULL(committed)) as vacancies, 'text' as text, MIN(id*(1000-999*ISNULL(committed))) as id,  DATE_FORMAT(`from`, '%Y-%m-01') as month FROM periodcontract
                                   WHERE (`from` > '".Carbon::now()->format('Y-m-d')."')  AND (houseid = ".$houseid.") GROUP BY month;"));

        foreach($months as $month)
        {
            if ($month->vacancies > 0)
            {
               $text = Carbon::parse($month->month)->formatLocalized('%B %Y').": ".__('There are vacancies');
            }
            else
            {
               $text = Carbon::parse($month->month)->formatLocalized('%B %Y').": ".__('Sold out');
            }
            $month->text = $text;
        }
        return response()->json($months);
    }

    /*
     * This method is used in views where the user has the option
     * of choosing periods for the contract.
     * The following
     */
    public function getweeks($houseid, $culture, $offset = 0, $periodid = 0, $contractid = 0, $periodsshown = 8)
    {
        Log::info("Ajax call getweeks(houseid; $houseid, culture: $culture, offset: $offset, periodid: $periodid, contractid: $contractid, periodsshown: $periodsshown)");

        $rate = 1;
        $warning = '';

        if ($periodid != 0)
        {
            $period = Periodcontract::find($periodid);
            $rate = $period->getRate($culture)['rate'];
            if ($period->contractid != null) $contractid = $period->contractid;
        }

        // We redefine the period
        if (($contractid != 0) && ($periodid == 0)) $period = Periodcontract::where('contractid', $contractid)->first();

        //When there is a contract, we us the currency defines in the contract
        if ($contractid != 0)
        {
            $contractid = $period->contractid;
            $contract = Contract::find($contractid);
            if ($contract) $rate = $period->getRate($culture, $contract->currencyid)['rate'];
        }

        //Prepare for showing several weeks, limit to 6 weeks using the paginate method
        $fromdate = $period->from->subDays(15-7*$periodsshown*$offset);
        $periodcontracts = Periodcontract::where('houseid', $houseid)
            ->whereDate('from', '>', $fromdate)
        //    ->whereDate('to', '>', Carbon::now())
            ->orderBy('from')
            ->paginate($periodsshown+1);

        //We check if some record are not included due to the to requirement
        $expelledperiods = Periodcontract::where('houseid', $houseid)
            ->whereDate('from', '>', $fromdate)
            ->whereDate('to', '<=', Carbon::now())
            ->orderBy('from')->count();

        if ($expelledperiods > 0) $warning = 'lower limit reached';

        //if ($fromdate->lt(Carbon::now())) $periodcontracts = null;

        $forJson = [];
        if (!$periodcontracts) $forJson = ['warning' => 'no records'];
        else
        {
            foreach ($periodcontracts as $p)
            {
                $committed = ($p->committed == 1)?true:false;
                $chosen = false;
                if ($periodid == $p->id) $chosen = true;
                if ($contractid == $p->contractid) $chosen = true;
                $forJson[] = ['id' => $p->id, 'committed' => $committed, 'periodtext' => $p->getEnddays($culture), 'persons' => $p->persons,
                    'chosen' => $chosen, 'personprice' => $p->personprice, 'maxpersons' => $p->maxpersons,
                    'basepersons' => $p->basepersons, 'baseprice' => $p->baseprice, 'warning' => $warning,
                    'rate' => $rate, 'from' => $p->from->format('Y-m-d'), 'to' => $p->to->format('Y-m-d')];
            }
        }


        return response()->json($forJson)
            ->header('Cache-Control', 'no-cache, must-revalidate');
    }
}
