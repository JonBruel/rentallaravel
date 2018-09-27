<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Schema;
use ValidationAttributes;
use App\Models\BaseModel;
use App\Models\Periodcontract;
use App\Models\Contract;
use Illuminate\Support\Facades\Input;


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

        //$table = 'house';
        //$customertypeid = $this->user->getAttribute('customertypeid', 1000);

        $defaultHouse = Input::query('defaultHouse',config('app.default_house', -1));


        $housequery = $this->model::whereBetween('latitude', [$x1, $x2])
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
            $house = new $this->model;
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
            //$housefields[$i]['veryshortdescription'] = $house->getVeryshortdescription($this->culture);
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


    /*
     * This method is used in views where the user has the option
     * of choosing periods for the contract.
     * The following
     */
    public function getweeks($houseid, $culture, $offset = 0, $periodid = 0, $contractid = 0)
    {
        $rate = 1;

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
        $periodcontracts = Periodcontract::where('houseid', $houseid)
            ->where('from', '>', $period->from->subDays(15-7*6*$offset))
            ->orderBy('from')
            ->paginate(6);

        $forJson = [];
        foreach ($periodcontracts as $p)
        {
            $committed = ($p->committed == 1)?true:false;
            $chosen = false;
            if ($periodid == $p->id) $chosen = true;
            if ($contractid == $p->contractid) $chosen = true;
            $forJson[] = ['id' => $p->id, 'committed' => $committed, 'periodtext' => $p->getEnddays($culture),
                'chosen' => $chosen, 'personprice' => $p->personprice, 'maxpersons' => $p->maxpersons,
                'basepersons' => $p->basepersons, 'baseprice' => $p->baseprice,
                'rate' => $rate];
        }

        return response()->json($forJson)
            ->header('Cache-Control', 'no-cache, must-revalidate');
    }
}
