<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
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
use App\Models\Accountpost;
use App\Models\Emaillog;
use App\Models\Periodcontract;
use App\Models\Batchtask;
use App\Models\Culture;
use App\Models\Customer;
use App;
use App\Models\Contractoverview;
use Carbon\Carbon;
use Number;

/**
 * Class ContractController. This is a controller with contains a wast amount of logic
 * in relation to the order process. In addition to this the generation of accountposts
 * is delegated to the Contract model, and we have tries to separate this from the order
 * process flow, which is implemented in this class.
 *
 * @package App\Http\Controllers
 */
class ContractController extends Controller
{
    //TODO: Let the user choose the house
    private $houseId = 1;

    public function __construct() {
        parent::__construct(\App\Models\Contract::class);
    }

    /**
     * Used to show the booking for the year, they year can be selected in the view. The view is trimmed to omit
     * the house (and the house select) if there is only one house.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function annualcontractoverview()
    {
        $thisyear = date('Y');
        $years = [];
        for ($i = $thisyear-10; $i < $thisyear+3; $i++) $years[$i] = $i;

        if (Input::get('year') == null) Input::merge(['year' => $thisyear]);

        $houses = House::filter()->pluck('name', 'id')->toArray();
        $houseid = Input::get('houseid', 1);

        $year = Input::get('year', $thisyear);
        $contractquery = Contractoverview::filter(Input::all())->where('categoryid', 0)->sortable()->orderBy('from')->with('customer')->with('house');
        $contractoverview = $contractquery->get();
        return view('contract/annualcontractoverview', ['year' => $year, 'years' => $years, 'contractoverview' => $contractoverview, 'houses' => $houses, 'houseid' => $houseid]);
    }

    /**
     * Display a specific contract. Presently not a part of the functions accessible via the menu system.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Set rights
        if (!Gate::allows('Administrator')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));

        //Find page from id
        $this->setPageFromId($id, Contract::class, 'Contract was not found');

        $models = Contract::filter(Input::all())->sortable('id')->paginate(1);
        $fields = Schema::getColumnListing($models[0]->getTable());
        $fields = array_diff($fields, ['created_at', 'updated_at', 'theme']);
        return view('contract/show', ['models' => $models, 'fields' => $fields]);
    }


    /**
     * Based on the input from the previous form, contractupdate, we commit the contract. If the user has not yet logged in,
     * he will be asked to register or login. After that he will be redirected back to this function. Eventually the user
     * will see a confirmation page with the contract which can be printed out.
     *
     * @param int $contractid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function commitcontract($contractid)
    {
        Contract::$ajax = false;
        //die("contractid: $contractid");
        $contract = Contract::findOrFail($contractid);
        //If the user has been autheticated we may save
        if (\Auth::check())
        {
            $user = Auth::user();
            //commitOrder wil also adjust the values in the contract such as customerid and status.
            $contract->customerid = $user->id;
            $contract->save();
            Contract::$ajax = true;
            $message = Contract::commitOrder(10, $user->id, $contract->id, $user->id);
            Contract::$ajax = false;
            Log::info('Contract committed by user: '.$user->id.' for customer: '.$contract->customerid.'. Contractid is: '.$contract->id.'. Message from commitOrder: '.$message );
        }

        //After having saved the contract we need to see if the customer email is not.logged.in@consiglia.dk, if so it should be replaced before the contract is committed.
        if ($contract->customer->email == "not.logged.in@consiglia.dk")
        {
            session(['redirectTo' => 'contract/commitcontract/'.$contractid.'?loginredirected=yes']);
            session()->flash('warning', 'Please login or register to finalize the order.');
            return redirect('login');
        }
        //Get payment details regarding prepayment
        $batchtask = Batchtask::where('name', 'Reservation cancelled')->where('houseid', $contract->houseid)->first();
        $duetext = '';
        if ($batchtask)
        {
            if ($batchtask->usetimedelaystart == 1) $duedate = Carbon::now()->addDays($batchtask->timedelaystart - 7);
            Contract::$ajax = true;
            $dueamount = $contract->finalprice*$batchtask->paymentbelow;
            Contract::$ajax = false;
            $duetext = __('The pre-payment amount of').' '.$contract->currency->currencysymbol.' '.Number::format($dueamount,['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2, 'locale' => App::getLocale()]).
                       ' '.__('must be paid before').' '.$duedate->format('d-m-Y').'. '.__('The mail includes the payment details').'.';
        }

        $printedcontract = $contract->getOrder($contract->customer->culture->culture);
        return view('contract/commitcontract', ['contract' => $contract, 'printedcontract' => $printedcontract, 'duetext' => $duetext]);
    }

    /**
     * Under certain conditions we allow for the user to cancel the contract. This will happen just after the contract has been committed,
     * or it may happen later, if the user tries to tweek the system or if we have provided him with a link to do so. So below
     * we check if the user is the same as the one who booked, and we check if the user is at least a customer.
     *
     * @param int $contractid id of contract
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelorder($contractid)
    {
        if (!Gate::allows('Customer')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $user = Auth::user();
        $contract = Contract::findOrFail($contractid);
        if ($contract->customerid != $user->id) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));

        //Get pre-payment date as expressed when the contract was committed
        $batchtask = Batchtask::where('name', 'Reservation cancelled')->where('houseid', $contract->houseid)->first();
        $duedate = Carbon::now()->addDays(7);;
        if ($batchtask)
        {
            if ($batchtask->usetimedelaystart == 1) $duedate = Carbon::now()->addDays($batchtask->timedelaystart - 7);
        }

        //The user should not be able to cancel if the real date is after the indicated pre-payment date
        if (Carbon::now()->gt($duedate)) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));

        //We are OK here and may detele.
        $contract->delete();
        return redirect('/home')->with('warning', __('You have cancelled the booking'));
    }


    /**
     * This method gives the input to the view used by the administrator to edit an
     * existing contract or the end user to create a new contract.
     *
     * Two use cases are handled here:
     * * We edit an existing contract, $contractid is then not 0
     * * We cheate a new contract, typically when the user has seen the calendar and
     *    presses a period. In this case $constratid is 0 and a non-zero $periodid is
     *    included in the argument.
     *
     * The view, which is used for the booking, is has @if's which depend on the use case.
     *
     * @param int $contractid
     * @param int $periodid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
            $errors = '';

            //Find customerid of logged in user or assign a temporary id id user is not logged in.
            //In the latter case, we will ask the user to login later. But not now, we want to delay
            //the hazzle of logging in to later in the order process.
            if (!(\Auth::check()))
            {
                $user = User::where("email","not.logged.in@consiglia.dk")->first();
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

        //Contract exist, being edited by an administrator
        else
        {
            //Set rights
            if (!Gate::allows('Administrator')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));

            //Find contract page from id
            $this->setPageFromId($contractid, Contract::class);

            $models = Contract::filter(Input::all())->sortable('id')->paginate(1);
            $model = $models[0];

            //Set default arrival and departure times if not set
            $contractoverview = Contractoverview::Find($contractid);
            $landingdatetime = $contractoverview->from->format('d-m-Y');
            $departuredatetime = $contractoverview->to->format('d-m-Y');

            if (!$model->landingdatetime) $model->landingdatetime = Carbon::parse($landingdatetime);
            if (!$model->departuredatetime) $model->departuredatetime = Carbon::parse($departuredatetime);
        }

        if  (Gate::allows('Administrator')) $fields = ['persons', 'discount', 'finalprice', 'currencyid', 'landingdatetime', 'departuredatetime', 'message'];
        else $fields = $fields = ['persons', 'discount', 'finalprice', 'currencyid'];

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


    /**
     * This method is for the customer, administrator, owner or supervisor. The finalprice is recalculated
     * to avoid possible attempts to inject javascript to fiddle with the price.
     *
     * @param $contractid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function contractupdate($contractid)
    {
        //TODO: should we handle the situation where there is no existing contract?
        $contract = Contract::Find($contractid);

        if (Input::get('Delete'))
        {
            $contract->delete();
            return redirect('contract/listcontractoverviewforowners?menupoint=11020')->with('success', __('Contract deleted').'.');
        }

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
        //$contract->save();

        // Update bookings and price information
        // Check for consecutive weeks, go back if not
        $lastday = null;
        if (!Input::get('checkedWeeks')) return back()->withInput()->with('warning',  __('Please choose at least one period').'.');
        foreach (Input::get('checkedWeeks') as $periodid) {
            $period = Periodcontract::find($periodid);
            $firstday = $period->from->format('d-m-Y');
            if ($lastday)
            {
                if ($lastday != $firstday)
                {
                    $weeksnotconsecutive = __('All booked rental periods must be consecutive, but they are not. Please check and reorder.');
                    return back()->withInput()->with('warning',  $weeksnotconsecutive);
                }
            }
            $lastday = $period->to->format('d-m-Y');
        }

        // Delete old period reservations as they might have been changed
        Contractline::where('contractid', $contractid)->delete();

        $price = 0;
        $persons = Input::get('persons');

        //We add the periods
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

        Contract::$ajax = true;
        $period = Periodcontract::find($firstperiodid);
        $rate = $period->getRate('', $currencyid)['rate'];
        $contract->price = $price * $rate;
        $newfinalprice = $price * $rate * (1-$contract->discount/100);
        $contract->finalprice = $newfinalprice;

        //We are administrating the contract
        if (Input::get('fromcalendar', 1) == 0)
        {
            //Set rights
            if (!Gate::allows('Administrator')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));

            //Check if we want to ignore the change, be sure we don't divide with 0
            if ($oldfinalprice == 0) $oldfinalprice = 1;
            if (abs(($oldfinalprice - $newfinalprice)/$oldfinalprice) > 0.001)
            {
                $contract->finalprice = $newfinalprice;
                $contract->status = 'AwaitsUpdate';
                Contract::$ajax = true;
                Contract::commitOrder(140, Auth::user()->id, $contractid, $contract->customerid);
                Contract::$ajax = false;
                $contract->save();
                $success = __('Contract updated').'.';
            }
            else $success = __('Contract not updated  as new final price is within 1 o/oo of old final price').'.';

            //We check the arrival and departuretime
            $contractoverview = Contractoverview::Find($contractid);
            //Empty values defaults to period start/end
            $landingdatetime = Input::get('landingdatetime_'.$contract->id);
            if ($landingdatetime == '') $landingdatetime = $contractoverview->from->format('d-m-Y');
            $departuredatetime = Input::get('departuredatetime_'.$contract->id);
            if ($departuredatetime == '') $departuredatetime = $contractoverview->to->format('d-m-Y');

            $contract->landingdatetime = Carbon::parse($landingdatetime);
            $contract->departuredatetime = Carbon::parse($departuredatetime);

            $contract->save();

            //If arrival or departure time are non-default, we register it as an account post event
            if (($contract->landingdatetime->ne($contractoverview->from)) || ($contract->departuredatetime->ne($contractoverview->to)))
            {
                Contract::$ajax = true;
                Contract::commitOrder(90, Auth::user()->id, $contract->id, $contract->customerid);
                Contract::$ajax = false;
            }

            $success .= ($currencyid != Input::get('currencyid'))?' '.__('Currency cannot be changed for a committed order!'):'';
            return redirect('contract/contractedit/'.$contractid.'/0')->with('success', $success);
        }
        //We have created starting from the calendar
        else
        {
            $contract->save();
            return $this->commitcontract($contract->id);
        }
    }

    /**
     * The focus of this is to feed a view which shows the arrivals times of the customers.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function listcontractoverview()
    {
        //'Personel'
        if (!Gate::allows('Personel')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));


        $houses = House::filter()->pluck('name', 'id')->toArray();
        $houseid = Input::get('houseid', 1);

        $contractoverview = Contractoverview::filter(Input::all())->sortable()->orderBy('houseid')->whereDate('from', '>', Carbon::now())->orderBy('from')->with('customer')->with('house')->get();

        return view('contract/listcontractoverview', ['contractoverview' => $contractoverview, 'houses' => $houses, 'houseid' => $houseid]);
    }

    /**
     * This controller is used for the rentaloverview, which as a default shows bookings from the present year and forward.
     * The view has several @ifs which controls the level of details shown to the user. Less is shown for the housekeeper than
     * fro the owner.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function listcontractoverviewforowners()
    {
        //Set rights
        if (!Gate::allows('Personel')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));


        $thisyear = date('Y');
        $years = [];
        for ($i = $thisyear-10; $i < $thisyear+3; $i++) $years[$i] = $i;

        if (Input::get('yearfrom') == null) Input::merge(['yearfrom' => $thisyear]);

        $houses = House::filter()->pluck('name', 'id')->toArray();
        $houseid = Input::get('houseid', 1);

        $year = Input::get('yearfrom', $thisyear);
        $contractquery = Contractoverview::filter(Input::all())->sortable()->orderBy('from')->with('customer')->with('house');
        $contractoverview = $contractquery->get();
        return view('contract/listcontractoverviewforowners', ['year' => $year, 'years' => $years, 'contractoverview' => $contractoverview, 'houses' => $houses, 'houseid' => $houseid]);
    }

    /**
     * Used to prepare a list of accountpost for a specific contract, feeding a view
     * which can be used to register payments.
     *
     * @param int $contractid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listaccountposts($contractid)
    {
        $currencysymbol = 'DKK';
        $models =  Accountpost::where('contractid', $contractid)->where('amount', "!=", 0)->get();
        return view('contract/listaccountposts', ['models' => $models,'currencysymbol' => $currencysymbol]);
    }

    /**
     * Used to feed a view showing the all mails submitted to the specified customer.
     *
     * @param int $customerid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listmails($customerid)
    {
        $emails = Emaillog::where('customerid', $customerid)->orderBy('created_at','desc')->get();
        return view('myaccount/listmails', ['models' => $emails, 'title' => __('Emails')]);
    }

    /**
     * This function takes the input from the listaccountposts form and created accountpost accordingly.
     *
     * @param int $contractid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerpayment($contractid)
    {

        //Set defaults for the created accountpost
        $contract = Contract::Find($contractid);
        $defaults = ['customerid' => $contract->customerid,
            'ownerid' => $contract->ownerid,
            'postsource' => 'Manually entered',
            'currencyid' => $contract->owner->culture->currencyid,
            'customercurrencyid' => $contract->currencyid,
            'contractid' => $contractid,
            'postedbyid' => Auth::user()->id,
            'passifiedby' => 0,
            'houseid' => $contract->houseid,
            'returndate' => $contract->getReturndate(),
            'usedrate' => 1];
        $accountpost = new Accountpost($defaults);

        $fields = ['amount', 'text', 'posttypeid'];
        foreach($fields as $field) $accountpost->$field = Input::get($field);
        $test = '';
        Contract::$ajax = true;
        $accountpost->amount = -$accountpost->amount;

        $success = __('New accountpost saved.');
        $errors = '';

        if (!$accountpost->save())
        {
            $success = __('Accountpost was not made');
            $errors = $accountpost->getErrors();
            return redirect('contract/listaccountposts/'.$contractid)->with('errors', $errors)->with('success', $success);
        }

        //Check if we want to insert rounding accountpost
        elseif (Input::get('round') == 1)
        {
            $contract = Contract::Find($contractid);
            Contract::$ajax = true;
            $contractamount = $contract->finalprice;
            $paid = 0;
            $accountposts = Accountpost::where('contractid', $contractid)->get();
            foreach ($accountposts as $accountpost)
            {
                $paid += -$accountpost->amount;
            }
            //die('Paid: '.$paid.' contractamount: '.$contractamount);
            if (abs(($paid/$contractamount)) < 0.001)
            {
                $accountpost = new Accountpost($defaults);
                $accountpost->posttypeid = 300;
                $accountpost->text = 'Automatic rounding';
                $accountpost->amount = $paid;
            }
            if (!$accountpost->save())
            {
                $success = __('Payment saved, but the rounding adjustment failed.');
                $errors = $accountpost->getErrors();
                return redirect('contract/listaccountposts/'.$contractid)->with('errors', $errors)->with('success', $success);
            }
        }
        return redirect('contract/listaccountposts/'.$contractid)->with('errors', $errors)->with('success', $success);
    }

    /**
     * This function feeds the view for editing accountposts, which should not be allowed for anyone, but the supervisor.
     * TODO: Determined if it should be allowed at all.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function accountpostedit($id)
    {
        return $this->generaledit($id, Accountpost::class, 'contract/accountpostedit', null, null, ['updated_at'], ['id' => 'hidden', 'contractid' => 'hidden']);
    }

    /**
     * Updates accountposts.
     * TODO: Determined if it should be allowed at all.
     *
     * @param int $id
     * @return ContractController
     */
    public function accountpostupdate($id)
    {
        //generalupdate($id, $modelclass, $okMessage, $redirectOk, $redirectError = null, $onlyFields = null, $plusFields = null, $minusFields = null)
        return $this->generalupdate($id, Accountpost::class, 'Accountpost updated', '/accountpost/edit/'.$id.'?contractid='.Input::get('contractid'), null, null, null, ['updated_at']);
    }

    /**
     * Destroys accountpost.
     * TODO: Determined if it should be allowed at all.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function accountpostdestroy($id)
    {
        $model =  Accountpost::findOrFail($id);
        $contractid = $model->contractid;
        $model->delete();
        return redirect('contract/listaccountposts/'.$contractid);
    }

}
