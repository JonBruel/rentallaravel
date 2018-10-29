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
use Schema;
use Gate;
use ValidationAttributes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Auth;
use App\Models\Customer;
use App\Models\Contract;
use App\Models\Contractoverview;
use App\Models\Emaillog;
use Carbon\Carbon;

/**
 * Class MyAccountController
 * @package App\Http\Controllers
 */
class MyAccountController extends Controller
{

    public function __construct() {
        parent::__construct(\App\Models\Customer::class);
    }

    /**
     * Starts a view showing the gdpr rules which are all located in the view.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
     public function gdpr()
     {
         session()->flash('warning', '');
         return view('home/gdpr');
     }

    /**
     * Feeds the view for showing the customer details for a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function registration()
    {
        if (!Auth::check()) return redirect('/login');
        session()->flash('warning', '');
        $user = Auth::user();
        $customer = Customer::Find($user->id);
        $fields = array_diff(Schema::getColumnListing($customer->getTable()), ['id', 'created_at', 'updated_at', 'remember_token', 'plain_password', 'password', 'ownerid', 'status', 'verified', 'houselicenses', 'customertypeid', 'lasturl', 'login' ]);
        //$fields = Schema::getColumnListing($customer->getTable());
        $vattr = (new ValidationAttributes($customer))->setCast('notes', 'textarea');


        //Check if customer is allowed to be deleted
        $allowdelete = false;
        $accountposts = $customer->accountposts();
        $count = $accountposts->where('posttypeid', 10)->count();
        $allowdelete = ($count == 0);

        return view('myaccount/registration', ['models' => [$customer], 'fields' => $fields, 'vattr' => $vattr, 'allowdelete' => $allowdelete]);
    }

    /**
     * Feeds a view which allows the customer to delete himselves provided there are
     * no accountposts for rental bookings.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroycustomer($id)
    {
        if (!Auth::check()) return redirect('/login');
        $user = Auth::user();
        if ($user->id != $id) return redirect('/login');

        //Check if customer is allowed to be deleted
        $customer = Customer::Find($user->id);
        $allowdelete = false;
        $accountposts = $customer->accountposts();
        $count = $accountposts->where('posttypeid', 10)->count();
        $allowdelete = ($count == 0);

        if ($allowdelete)
        {
            $manager = app('impersonate');
            if ($manager->isImpersonating())
            {
                $manager->leave();
            }
            else auth()->logout();
            $customer->delete();
        }
        return redirect('/home')->with('warning',  __('All data about you is now deleted. Before you book next time, you must register anew.'));
    }

    /**
     * The function which updates information about a customer as requested by the customer.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateregistration()
    {
        if (!Auth::check()) return redirect('/login');
        session()->flash('warning', '');
        if (Input::get('delete')) return $this->destroycustomer(Input::get('id'));

        $customer = Customer::Find(Input::get('id'));

        $fields = array_diff(Schema::getColumnListing($customer->getTable()), ['id', 'created_at', 'updated_at', 'remember_token', 'plain_password', 'password', 'ownerid', 'status', 'verified', 'houselicenses', 'customertypeid', 'lasturl', 'login' ]);
        foreach ($fields as $field)
        {
            $customer->$field = Input::get($field);
        }

        //We save. The save validates after the Mutators have been used.
        $errors = '';
        $success = _('Your contact data have been updated');
        if (!$customer->save()) {
            $errors = $customer->getErrors();
            $success = '';
        }
        if ($errors != '') return redirect('/myaccount/registration')->with('success', $success)->with('errors',$errors)->withInput(Input::except('plain_password'));
        return redirect('/myaccount/registration?menupoint=9010')->with('success', __('Customer has been updated').'!');
    }

    /**
     * A helper function for the edittime(), listaccountposts(), listbookings() and updatetime() functions.
     *
     * @param Carbon|null $date is the from date to get the contracts
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function getContracts(Carbon $date = null)
    {
        if (!Auth::check()) return redirect('/login');
        $user = Auth::user();

        //We want to sort the contractlines starting with latest rental from date
        if ($date) $contractoverviews = Contractoverview::where('customerid', $user->id)->whereDate('to', '>', $date)->orderBy('from', 'desc')->get();
        else $contractoverviews = Contractoverview::where('customerid', $user->id)->orderBy('from', 'desc')->get();
        //$contracts = [];
        //foreach ($contractoverviews as $contractoverview) $contracts[] = Contract::Find($contractoverview->id);
        return $contractoverviews;
    }

    /**
     * Feeds the view which shoow all the rental contract ever for the customer.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listbookings()
    {
        session()->flash('warning', '');
        $contracts = $this->getContracts();
        if ( sizeof($contracts) == 0) session()->flash('warning', __('There are no contracts to show.'));
        return view('myaccount/listbookings', ['models' => $contracts]);
    }

    /**
     * Feeds the view for showing all the accountposts including the accountpost with no amount.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listaccountposts()
    {
        session()->flash('warning', '');
        Contract::$ajax = true;
        $contracts = $this->getContracts();
        if (sizeof($contracts) == 0) session()->flash('warning', __('There are no account posts to show.'));
        return view('myaccount/listaccountposts', ['models' => $contracts]);
    }

    /**
     * Feeds the view for showing all mails to the customer.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function listmails()
    {
        session()->flash('warning', '');
        if (!Auth::check()) return redirect('/login');
        $user = Auth::user();
        $emails = Emaillog::where('to', $user->email)->get();
        return view('myaccount/listmails', ['models' => $emails, 'title' => __('My emails')]);
    }

    /**
     * Feeds the view which lets the customer update the arrival and departure times of future rental bookings.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function edittime()
    {
        //Following if used for edittiemelink
        session()->flash('warning', '');
        $res = $this->checkToken();
        if($res) return redirect($res);

        $contracts = $this->getContracts(Carbon::now());
        $contract = null;
        $vattr = null;
        if (sizeof($contracts) > 0)
        {
            $contract = $contracts[0];
            $vattr = new ValidationAttributes($contract);
        }
        else session()->flash('warning', __('There are no contracts to show.'));
        return view('myaccount/edittime', ['models' => $contracts, 'vattr' => $vattr]);
    }

    /**
     * Updates arrival and departure times and inserts an accountpost for the workflow system
     * to register the action.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatetime()
    {
        $contracts = $this->getContracts(Carbon::now());
        $errors = '';
        $success = __('Your arrival and departure schedule has been updated');
        foreach ($contracts as $contractoverview)
        {
            $contract = $contractoverview->contract;

            //Empty values defaults to period start/end
            $landingdatetime = Input::get('landingdatetime_'.$contract->id);
            if ($landingdatetime == '') $landingdatetime = $contractoverview->from->format('d-m-Y');
            $departuredatetime = Input::get('departuredatetime_'.$contract->id);
            if ($departuredatetime == '') $departuredatetime = $contractoverview->to->format('d-m-Y');

            $contract->landingdatetime = Carbon::parse($landingdatetime);
            $contract->departuredatetime = Carbon::parse($departuredatetime);
            if (!$contract->save()) {
                $errors = $contract->getErrors();
                $success = '';
            }
            else
            {
                //If arrival or departure time, as recorded by user, is non-default, we register it as an account post event
                if (($contract->landingdatetime->ne($contractoverview->from)) || ($contract->departuredatetime->ne($contractoverview->to)))
                {
                    Contract::commitOrder(90, Auth::user()->id, $contract->id, Auth::user()->id);
                }

            }
        }
        return redirect('myaccount/edittime')->with(Input::all())->with('errors', $errors)->with('success', $success);
    }
}
