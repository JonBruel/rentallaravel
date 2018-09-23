<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
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
use App\Models\Contractline;
use App\Models\User;
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

        if ($request->get('year') == null) $request['year'] = $thisyear;

        $houses = House::filter()->pluck('name', 'id')->toArray();
        $houseid = Input::get('houseid', 1);

        $year = Input::get('year', $thisyear);
        $contractquery = Contractoverview::filter($request->all())->sortable()->orderBy('from')->with('customer')->with('house');
        $contractoverview = $contractquery->get();
        return view('contract/annualcontractoverview', ['year' => $year, 'years' => $years, 'contractoverview' => $contractoverview, 'houses' => $houses, 'houseid' => $houseid]);
    }

    /*
     * This is the first landing place after the customer has chosen a week i the calendar.
     * The method id "periodcontract-excentric", as there is no contract.
     */
    public function chooseweeks() {

        //$this->getResponse()->setHttpHeader('Cache-Control', 'no-cache, must-revalidate');
        $table = 'Periodcontract';
        $id = Input::get('contractid', 0);
        $returnpath = 'contract/chooseweeks?menupoint='.session('menupoint', 10020);
        $this->checkHouseChoice($returnpath);
        $houseid = session('defaultHouse');

        if (session('step', 0) == '0') session(['step' => 1]);

        $periodid = $this->doSaveAndRetrieve('periodid', 0);
        
        //$restrictscopetocustomer = $this->doSaveAndRetrieve('restrictscopetocustomer', 0);

        if ($periodid == 0)
        {
            session()->flash('warning', 'Please click the first week you want to rent!');
            return redirect('home/checkbookings')->with('returnpath', $returnpath);
        }
        $period = Periodcontract::find($periodid);
        $maxpersons = $period->maxpersons;

        $price = 0;

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

        return view('contract/chooseweeks',['periodcontracts' => $periodcontracts, 'checkedWeeks' => $checkedWeeks, 'contractid' => $id, 'personSelectbox' => $personSelectbox]);
    }

    public function preparecontract() {
        if (session('step', 0) == '1') session(['step => 2']);

        $houseid = Input::get('houseid');
        $contractid = Input::get('contractid', 0);
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

        if ($contractid == 0)
        {
            try
            {
                $contract = new Contract(
                    [
                        'houseid' => $houseid,
                        'ownerid' => House::find($houseid)->ownerid,
                        'status' => 'New',
                        'customerid' => $user->id,
                        'persons' => $persons,
                        'discount' => 0,
                        'categoryid' => 0,
                        'currencyid' => $culture->currencyid
                    ]);

                //We temporally save to get the id
                $errors = '';
                if (!$contract->save()) {
                    Log::notice('Aborting in ContractController, error during Save(), $contract not saved.');
                    $errors = $contract->getErrors();
                }
            }
            catch(\Exception $e){
                Log::warning('There was an exception when saving the $contract: '.$e->getMessage());
            }
            if ($errors != '') return back()->withInput()->with('errors',  $errors);
            $contractid = $contract->id;
        }
        else $contract = Contract::Find($contractid);

        //The $contract is now either a new one or an existing. We now delete all existing periods
        //from the contractlines, allowing for new - and possibly different ones to be added, But before
        //doing that, we check that the weeks to be added are consecutive.

        // Check for consecutive weeks, go back if not
        $lastday = null;
        foreach (Input::get('checkedWeeks') as $periodid) {
            $period = Periodcontract::find($periodid);
            $firstday = $period->from->format('Y-m-d');
            if ($lastday)
            {
                if ($lastday != $firstday)
                {
                    $weeksnotconsecutive = __('All booked rental periods must be consecutive, but they are not. Please check and reorder.');
                    return back()->withInput()->with('weeksnotconsecutive',  $weeksnotconsecutive);
                }
            }
            $lastday = $period->to->format('Y-m-d');
        }

        //Delete old contractlines
        Contractline::where('contractid', $contractid)->delete();

        $price = 0;
        $firstperiodid = 0;
        foreach (Input::get('checkedWeeks') as $periodid) {
            if (0 != $contract->addWeek($periodid)) {
                //$request>session()->flash('warning', 'Please clich the first week you want to rent!');
                session()->flash('warning', 'The chosen period is already booked, please re-order.');
                return redirect('contract/choseweeks?houseid='.$houseid.'&id='.$contractid);
            }
            if ($firstperiodid == 0) $firstperiodid = $periodid;
            $period = Periodcontract::find($periodid);
            $price += (max(0, $persons - $period->basepersons))*$period->personprice + $period->baseprice;
        }
        //Weeks are now added, and the rate(s) below will be the rates of the created_at date of the contract.

        //Determine rates, we later use the firstperiod to return to the same form if the user presses "back".
        $period = Periodcontract::find($firstperiodid);
        $contract->price = $price * $period->getRate($culture->culture)['rate'];
        $contract->finalprice = $price * $period->getRate($culture->culture)['rate'];

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


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Find page from id
        if (Input::get('page') == null) {
            $models = $this->model::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
        }

        $models = $this->model::filter(Input::all())->sortable('id')->paginate(1);
        $fields = Schema::getColumnListing($models[0]->getTable());
        $fields = array_diff($fields, ['created_at', 'updated_at', 'theme']);
        return view('contract/show', ['models' => $models, 'fields' => $fields]);
    }

    /*
     * This method creates a new contract. The use case is: Customer hos checked the vacancy
     * and pressed a period and he ends here, where contract will be created before he is
     * sent over to the next view, where he can edit the details.
     *
     * The contract status is "New" and it will be deleted by a cron if it remains so after
     * some minutes.
     */
    public function newcontract($periodid)
    {
        $period = Periodcontract::find($periodid);
        $houseid = $period->houseid;
        $maxpersons = $period->maxpersons;

        //Find customerid of logged in user or assign a temporary id id user is not logged in.
        //In the latter case, we will ask the user to login later. But not now, we want to delay
        //the hazzle of loggin in to later.
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

        //We now have a user, a periodid, and we can create an order. Thereby blocking
        //any other user to book the same period. Byt we don't do it
        //if it has already been created and still exists.
        $createNewContract = false;
        $contractid = session('contractid', 0);
        if ($contractid == 0) $createNewContract = true;
        else if (!Contract::Find($contractid)) $createNewContract = true;

        if ($createNewContract){
            $culture = Culture::find($user->cultureid);
            try {
                $contract = new Contract(
                    [
                        'houseid' => $houseid,
                        'ownerid' => House::find($houseid)->ownerid,
                        'status' => 'New',
                        'customerid' => $user->id,
                        'persons' => 2,
                        'discount' => 0,
                        'categoryid' => 0,
                        'currencyid' => $culture->currencyid
                    ]);

                //We temporally save to get the id
                $errors = '';
                if (!$contract->save()) {
                    Log::notice('Aborting in ContractController, error during Save(), $contract not saved.');
                    $errors = $contract->getErrors();
                }
            } catch (\Exception $e) {
                Log::warning('There was an exception when saving the $contract: ' . $e->getMessage());
            }
            if ($errors != '') return back()->withInput()->with('errors', $errors);


            //Add the period, go back if the period is already taken.
            if (0 != $contract->addWeek($periodid)) {
                session()->flash('warning', 'The chosen period has in this moment been occupied by another customer, please re-order.');
                return back();
            }

            $contractid = $contract->id;
            session(['contractid' => $contractid]);
        }
        $contractid = session('contractid', 0);

        return $this->customeredit($contractid);
    }

    /*
   * This method gives the input to the view used by the customer to edit a
   * new contract.
   */
    public function customeredit($contractid)
    {
        $model = Contract::Find($contractid);
        $models = [$model];
        if (!$model) die('No contract found with id: '.$contractid.' please handle this!');
        $fields = ['persons', 'finalprice', 'currencyid', 'landingdatetime', 'departuredatetime'];

        //We need the currency rate for the view, calculation based on price and discount:
        //$rate = $model->finalprice/((1-$model->discount/100)*$model->price);
        $rate = 1;
        $contractlines = $model->contractlines();
        // echo(var_dump($contractlines->get()->first()->periodid));

        //Get max persons
        $firstperiod = Periodcontract::Find($contractlines->get()->first()->periodid);
        //Data for persons selectbox
        $personSelectbox = [];
        for ($i=$firstperiod->basepersons; $i <= $firstperiod->maxpersons; $i++)
        {
            $personSelectbox[$i] = $i;
        }

        $choosecurrency = true;
        $rates = [];
        $currencySelect = [];
        if ($choosecurrency)
        {
            //Calculate the rates as a function of the currencyid
            //Set the array used for the currencySelect in the view
            foreach (Culture::all() as $cult)
            {
                //$cultuteidToCurrencyid[$cult->culture] = $culture->currencyid;
                $rates[$cult->currencyid] = $firstperiod->getRate($cult->culture)['rate'];
                $currencySelect[$cult->currencyid] = $firstperiod->getRate($cult->culture)['currencysymbol'];
            }
        }

        return view('contract/customeredit', ['models' => $models, 'rate' => $rate, 'rates' => $rates, 'fields' => $fields,
            'vattr' => new ValidationAttributes($models[0]), 'personSelectbox' => $personSelectbox,
            'currencySelect' => $currencySelect]);
    }

    /*
     * Based on the input from the previous form, contractupdate, we commit the contract after having checked for
     *
     * If the user has not yet logged in, he will be asked to register or login. After that he will be redirected to
     * the same page.
     */
    public function commitcontract($id)
    {
        Contract::$ajax = false;
        $contractid = Input::get('id');
        $contract = Contract::findOrFail($contractid);
        //If the user has been autheticated we may save
        if (\Auth::check())
        {
            $user = Auth::user();
            //commitOrder wil also adjust the values in the contract such as customerid and status.
            $message = Contract::commitOrder(10, $user->id, $contract->id, $user->id);
            Log::info('Contract committed by user: '.$user->id.' for customer: '.$contract->customerid.'. Contractid is: '.$contract->id.'. Message from commitOrder: '.$message );
        }

        //After having saved the contract we need to see if the userid is 10, if so it should be replaced before the contract is committed.
        if ($contract->customerid == 10)
        {
            session(['redirectTo' => 'contract/confirmcontract/'.$contractid.'?loginredirected=yes']);
            session()->flash('warning', 'Please login or register to finalize the order.');
            return redirect('login');
        }
        $printedcontract = $contract->getOrder($contract->customer->culture->culture);
        return view('contract/commitcontract', ['contract' => $contract, 'printedcontract' => $printedcontract]);
    }


    /*
     * This method gives the input to the view used by the administrator to edit an
     * exisiting contract.
     */
    public function contractedit($contractid, $periodid)
    {
        $fromcalendar = false;

        //We have entered this method from the calendar, a new contract will be made
        if ($contractid == 0)
        {
            //No pagination, TODO: remove pagination from view
            $fromcalendar = true;
            $period = Periodcontract::find($periodid);
            $houseid = $period->houseid;
            $maxpersons = $period->maxpersons;

            //Find customerid of logged in user or assign a temporary id id user is not logged in.
            //In the latter case, we will ask the user to login later. But not now, we want to delay
            //the hazzle of loggin in to later.
            if (!(\Auth::check()))
            {
                //TODO: Check if we can do away with this session setting
                session(['conserveuserinfo' => 1]);
                $user = User::find(10);
                //TODO: Modify logic around users not logged in
                //$this->loginRedirect('temp', 'hX16BvylOmps', 'contract/preparecontract');
            }
            else
            {
                $user = Auth::user();
            }

            //Check if any other contracts with the status New exists, delete if so.
            //This may happen if the user goes back to the calendar
            Contract::where('status', 'New')->where('houseid', $houseid)->where('customerid', $user->id)->delete();

            //Create contract
            $culture = Culture::find($user->cultureid);
            try {
                $contract = new Contract(
                    [
                        'houseid' => $houseid,
                        'ownerid' => House::find($houseid)->ownerid,
                        'status' => 'New',
                        'customerid' => $user->id,
                        'persons' => 2,
                        'price' => 0,
                        'finalprice' => 0,
                        'discount' => 0,
                        'categoryid' => 0,
                        'currencyid' => $culture->currencyid
                    ]);

                //We temporally save to get the id
                $errors = '';
                if (!$contract->save()) {
                    Log::notice('Aborting in ContractController, error during Save(), $contract not saved.');
                    $errors = $contract->getErrors();
                }
            } catch (\Exception $e) {
                Log::warning('There was an exception when saving the $contract: ' . $e->getMessage());
            }
            if ($errors != '') return back()->withInput()->with('errors', $errors);


            //Add the period, go back if the period is already taken.
            if (0 != $contract->addWeek($periodid)) {
                return back()->with('warning', 'The chosen period has in this moment been occupied by another customer, please re-order.');;
            }

            $contractid = $contract->id;
            session(['contractid' => $contractid]);
            $model = $contract;
            $models = [$model];
        }
        //Contract with status New is now created, it will need to be committed

        else
        {
            //Find page from id, we use the contractoverview her, as it gives us the from and to date.
            //$this->model = Contractoverview::class;
            if (Input::get('page') == null)
            {
                $models = $this->model::filter(Input::all())->sortable('id')->pluck('id')->all();
                $page = array_flip($models)[$contractid]+1;
                Input::merge(['page' => $page]);
            }

            $models = $this->model::filter(Input::all())->sortable('id')->paginate(1);
            $model = $models[0];
        }

        $fields = ['persons', 'discount', 'finalprice', 'currencyid', 'landingdatetime', 'departuredatetime', 'message'];
        //$fields = Schema::getColumnListing($models[0]->getTable());

        //We need the currency rate for the view, calculation based on price and discount:
        //$rate = $model->finalprice/((1-$model->discount/100)*$model->price);
        $rate = 1;
        $contractlines = $model->contractlines();
        // echo(var_dump($contractlines->get()->first()->periodid));

        //Get max persons
        $firstperiod = Periodcontract::Find($contractlines->get()->first()->periodid);
        //Data for persons selectbox
        $personSelectbox = [];
        for ($i=$firstperiod->basepersons; $i <= $firstperiod->maxpersons; $i++)
        {
            $personSelectbox[$i] = $i;
        }

        $choosecurrency = true;
        $rates = [];
        $currencySelect = [];
        if ($choosecurrency)
        {
            //Calculate the rates as a function of the currencyid
            //Set the array used for the currencySelect in the view
            foreach (Culture::all() as $cult)
            {
                //$cultuteidToCurrencyid[$cult->culture] = $culture->currencyid;
                $rates[$cult->currencyid] = $firstperiod->getRate($cult->culture)['rate'];
                $currencySelect[$cult->currencyid] = $firstperiod->getRate($cult->culture)['currencysymbol'];
            }
        }

        return view('contract/contractedit', ['models' => $models, 'rate' => $rate, 'rates' => $rates, 'fields' => $fields,
                                            'vattr' => (new ValidationAttributes($models[0]))->setCast('message', 'textarea'),
                                            'personSelectbox' => $personSelectbox,
                                            'fromcalendar' => $fromcalendar,
                                            'periodid' => $periodid,
                                            'currencySelect' => $currencySelect]);
    }

    /*
     * This method is for the administrator, owner or supervisor. The finalprice is recalculated
     * to avoid possible attempts to inject javascript to fiddle with the price
     * TODO: set access filter
     */
    public function contractupdate($contractid)
    {
        //TODO: should we handle the situation where there is no existing contract?
        $contract = Contract::Find($contractid);
        Contract::$ajax = true;
        $oldfinalprice = $contract->finalprice;
        Contract::$ajax = false;

        //$contract->finalprice = Input::get('finalprice');
        if ($contract->status == 'New') $currencyid = Input::get('currencyid');
        else $currencyid = $contract->currencyid;
        $contract->currencyid = $currencyid;
        $contract->discount = Input::get('discount');
        $contract->persons = Input::get('persons');
        $contract->message = Input::get('message');


        $contractoverview = Contractoverview::Find($contractid);
        //Empty values defaults to period start/end
        $landingdatetime = Input::get('landingdatetime_'.$contract->id);
        if ($landingdatetime == '') $landingdatetime = $contractoverview->from->format('Y-m-d');
        $departuredatetime = Input::get('departuredatetime_'.$contract->id);
        if ($departuredatetime == '') $departuredatetime = $contractoverview->to->format('Y-m-d');

        $contract->landingdatetime = Carbon::parse($landingdatetime);
        $contract->departuredatetime = Carbon::parse($departuredatetime);
        //$contract->save();

        // Update bookings and price information
        // Check for consecutive weeks, go back if not
        $lastday = null;
        foreach (Input::get('checkedWeeks') as $periodid) {
            $period = Periodcontract::find($periodid);
            $firstday = $period->from->format('Y-m-d');
            if ($lastday)
            {
                if ($lastday != $firstday)
                {
                    $weeksnotconsecutive = __('All booked rental periods must be consecutive, but they are not. Please check and reorder.');
                    return back()->withInput()->with('weeksnotconsecutive',  $weeksnotconsecutive);
                }
            }
            $lastday = $period->to->format('Y-m-d');
        }

        // Delete old period reservations as they might have been changed
        Contractline::where('contractid', $contractid)->delete();

        $price = 0;
        $persons = Input::get('persons');

        $firstperiodid = 0;
        foreach (Input::get('checkedWeeks') as $periodid) {
            if (0 != $contract->addWeek($periodid)) {
                //$request>session()->flash('warning', 'Please clich the first week you want to rent!');
                session()->flash('warning', 'The chosen period is already booked, please re-order.');
                return redirect('contract/adminedit/'.$contractid); //There is a difference here
            }
            if ($firstperiodid == 0) $firstperiodid = $periodid;
            $period = Periodcontract::find($periodid);
            $price += (max(0, $persons - $period->basepersons))*$period->personprice + $period->baseprice;
        }
        //Weeks are now added, and the rate(s) below will be the rates of the created_at date of the contract.

        //Determine rates, we later use the firstperiod to return to the same form if the user presses "back".
        Contract::$ajax = true;
        $period = Periodcontract::find($firstperiodid);
        $rate = $period->getRate('', $currencyid)['rate'];
        $contract->price = $price * $rate;
        $newfinalprice = $price * $rate * (1-$contract->discount/100);

        //We are administrating the contract
        if (Input::get('fromcalendar', 1) == 0)
        {
            //Check if we want to ignore the change, be sure we don't divide with 0
            if ($oldfinalprice == 0) $oldfinalprice = 1;
            if (abs(($oldfinalprice - $newfinalprice)/$oldfinalprice) > 0.001)
            {
                $contract->finalprice = $newfinalprice;
                $contract->status = 'AwaitsUpdate';
                Contract::commitOrder(140, Auth::user()->id, $contractid, $contract->customerid);
                $contract->save();
                $success = __('Contract updated').'.';
            }
            else $success = __('Contract not updated  as new final price is within 1 o/oo of old final price').'.';
            $contract->save();

            //If arrival or departure time are non-default, we register it as an account post event
            if (($contract->landingdatetime->ne($contractoverview->from)) || ($contract->departuredatetime->ne($contractoverview->to)))
            {
                Contract::commitOrder(90, Auth::user()->id, $contract->id, $contract->customerid);
            }

            $success .= ($currencyid != Input::get('currencyid'))?' '.__('Currency cannot be changed for a committed order!'):'';
            return redirect('contract/contractedit/'.$contractid.'/0')->with('success', $success);
        }
        else
        {
            $contract->save();
            return $this->commitcontract($contract->id);
        }
    }




    public function listcontractoverview()
    {

        return view('contract/listcontractoverview', ['contracts' => $contracts]);
    }

    public function listcontractoverviewforowners(Request $request)
    {
        $this->model = \App\Models\Contract::class;
        $thisyear = date('Y');
        $years = [];
        for ($i = $thisyear-10; $i < $thisyear+3; $i++) $years[$i] = $i;

        if ($request->get('yearfrom') == null) $request['year'] = $thisyear;

        $houses = House::filter()->pluck('name', 'id')->toArray();
        $houseid = Input::get('houseid', 1);

        $year = Input::get('yearfrom', $thisyear);
        $contractquery = Contractoverview::filter($request->all())->sortable()->orderBy('from')->with('customer')->with('house');
        $contractoverview = $contractquery->get();
        return view('contract/listcontractoverviewforowners', ['year' => $year, 'years' => $years, 'contractoverview' => $contractoverview, 'houses' => $houses, 'houseid' => $houseid]);
    }




}
