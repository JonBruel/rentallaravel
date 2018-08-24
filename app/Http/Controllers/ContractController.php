<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use App\Helpers\PictureHelpers;
use App\Helpers\ShowCalendar;
use Illuminate\Pagination\Paginator;
use Auth;
use Schema;
use Gate;
use ValidationAttributes;
use App\Models\House;
use App\Models\Contract;
use App\User;
use App\Models\Currency;
use App\Models\Period;
use App\Models\Periodcontract;
use App\Models\Culture;
use App;
use App\Models\Contractoverview;
use Carbon\Carbon;

class ContractController extends Controller
{
    //TODO: Let the user choose the house
    private $houseId = 1;

    public function __construct() {
        parent::__construct(\App\Models\Contract::class);
    }

    public function annualcontractoverview(Request $request)
    {
        $this->model = \App\Models\Contractoverview::class;
        $thisyear = date('Y');
        $years = [];
        for ($i = $thisyear-3; $i < $thisyear+3; $i++) $years[$i] = $i;

        $houses = House::filter()->pluck('name', 'id')->toArray();
        $houseid = Input::get('houseid', 1);

        $year = Input::get('year', $thisyear);
        $contractquery = Contractoverview::filter($request->all())->sortable()->orderBy('from')->with('customer')->with('house');
        $contractoverview = $contractquery->get();
        return view('contract/annualcontractoverview', ['year' => $year, 'years' => $years, 'contractoverview' => $contractoverview, 'houses' => $houses, 'houseid' => $houseid]);
    }

    public function chooseweeks(Request $request) {

        //$this->getResponse()->setHttpHeader('Cache-Control', 'no-cache, must-revalidate');
        $table = 'Periodcontract';
        session(['FormSave' => $request->get('FormSave', 'not set')]);
        $returnpath = 'contract/chooseweeks?menupoint='.session('menupoint', 10020);
        $this->checkHouseChoice($request, $returnpath);
        $houseid = session('defaultHouse');

        if (session('step', 0) == '0') session(['step' => 1]);

        $periodid = $this->doSaveAndRetrieve('periodid', 0);
        
        //$restrictscopetocustomer = $this->doSaveAndRetrieve('restrictscopetocustomer', 0);

        if ($periodid == 0)
        {
            $request>session()->flash('warning', 'Please click the first week you want to rent!');
            return redirect('home/checkbookings')->with('returnpath', $returnpath);
        }
        $period = Period::find($periodid);
        $maxpersons = $period->maxpersons;

        $price = 0;

        //The code below takes action when the user goes back from step 2, possibly to change the order
        if (Input::get('contractid', 0) > 0) {
            $contract = Contract::find(Input::get('contractid'));
            if ($contract) $contract->delete();
            session(['step' => 1]);
        }


        $checkedWeeks = array();

        //Prepare for showing several weeks, limit to 6 weeks using the paginate method
        $periodcontracts = Periodcontract::where('houseid', $houseid)
                            ->where('from', '>', $period->from->subDays(15))
                            ->orderBy('from')
                            ->paginate(6);

        //We populate the $checkedWeeks with one checked value.
        //In addition, we determine the maxpersons as the min. value over the 6 records
        foreach ($periodcontracts as $periodcontract)
        {
            $checkedWeeks[$periodcontract->id] = 0;
            if ($periodcontract->id == $periodid)
            {
                $checkedWeeks[$periodcontract->id] = 1;
            }
            $maxpersons = min($maxpersons, $periodcontract->maxpersons);
        }

        //Data for persons selectbox
        $personSelectbox = [];
        for ($i=2; $i <= $maxpersons; $i++)
        {
            $personSelectbox[$i] = $i;
        }

        return view('contract/chooseweeks',['periodcontracts' => $periodcontracts, 'checkedWeeks' => $checkedWeeks, 'personSelectbox' => $personSelectbox]);
    }

    public function preparecontract(Request $request) {
        $table = 'periodcontract';
        $periodids = array();

        if (session('step', 0) == '1') session(['step => 2']);

        $houseid = Input::get('houseid');

        //Handle invalid input

        $persons = Input::get('persons', 2);

        //if not authenticated, we use the temporary user
        if (!(\Auth::check()))
        {
            session(['conserveuserinfo' => 1]);
            $user = User::find(10);
            //TODO: Modify logic around users not logged in
            //$this->loginRedirect('temp', 'hX16BvylOmps', 'contract/preparecontract');
        }
        else
        {
            $user = Auth::user();
        }

        $culture = Culture::find($user->cultureid);


        //We check that the same customer has not already saved
        //If so we delete the contract and start over
        //TODO: Implement it

        //We turn off mutators
        Contract::$ajax = true;
        $contract = new Contract(
        [
            'houseid' => $houseid,
            'ownerid' => House::find($houseid)->ownerid,
            'status' => 'New',
            'customerid' => $user->id,
            'persons' => $persons,
            'discount' => 0,
            'categoryid' => 0,
            'currencyid' => $culture->currencyid,
        ]);

        //We temporally save to get the id
        $contract->save();

        $price = 0;
        $firstperiodid = 0;
        foreach (Input::get('checkedWeeks') as $periodid) {
            if (0 != $contract->addWeek($periodid)) {
                //$request>session()->flash('warning', 'Please clich the first week you want to rent!');
                session()->flash('warning', 'The chosen period is already booked, please re-order.');
                return redirect('contract/choseweeks?houseid=' . $houseid);
            }
            if ($firstperiodid == 0) $firstperiodid = $periodid;
            $period = Periodcontract::find($periodid);
            $price += (max(0, $persons - $period->basepersons))*$period->personprice + $period->baseprice;
        }

        //Determine rates, we later use the firstperiod to return to the same form if the user presses "back".
        $period = Periodcontract::find($firstperiodid);
        $contract->price = $price;
        $contract->finalprice = $price * $period->getRate($culture->culture)['rate'];;

        //Contract is now saved as a proposal, status is "New" and a background service will delete it is the status does
        //not change to "Committed". Anyhow, the periods are now locked to the session/user.
        $contract->save();

        //Calculate the rates as a function of the currencyid
        //Set the array used for the currencySelect in the view
        $rates = [];
        $currencySelect = [];
        foreach (Culture::all() as $cult)
        {
            //$cultuteidToCurrencyid[$cult->culture] = $culture->currencyid;
            $rates[$cult->currencyid] = $period->getRate($cult->culture)['rate'];
            $currencySelect[$cult->currencyid] = $period->getRate($cult->culture)['currencysymbol'];
        }

        Contract::$ajax = false;
        return view('contract/preparecontract',['contract' => $contract, 'currencySelect' => $currencySelect, 'currencyid' => $culture->currencyid, 'rates' => $rates, 'firstperiodid' => $firstperiodid]);
    }

    public function commitcontract(Request $request)
    {
        die('Finalprice: '.$request->get('finalprice)'));

        Contract::$ajax = false;
        $id = Input::get('id');
        $contract = Contract::findOrFail($id);

        //Update relevant fields, only when comin from previous workflow step
        if (Input::get('loginredirected', 'no') == 'no')
        {
            die('Finalprice: '.$request->get('finalprice)'));
            $contract->finalprice = Input::get('finalprice)');
            $contract->currencyid = Input::get('currencyid)');
            $contract->discount = Input::get('discount)');
            $contract->save();
        }

        //After having saved the contract we need to see if the userid is 10, if so it should be replaced before the contract is committed.
        if ($contract->customerid == 10)
        {
            session(['redirectTo' => 'contract/commitcontract?loginredirected=yes&id='.$id]);
            $request>session()->flash('warning', 'Please login or register to finalize the order.');
            return redirect('login');
        }

        $mailtext = "This is the mail text generated by the batch system";
        return view('contract/commitcontract', ['mailtext' => $mailtext]);
    }

}
