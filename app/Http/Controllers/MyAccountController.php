<?php

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


class MyAccountController extends Controller
{


    public function __construct() {
        parent::__construct(\App\Models\Customer::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function registration()
    {
        if (!Auth::check()) return redirect('/login');
        $user = Auth::user();
        $customer = $this->model::Find($user->id);
        $fields = array_diff(Schema::getColumnListing($customer->getTable()), ['id', 'created_at', 'updated_at', 'remember_token', 'plain_password', 'password', 'ownerid', 'status', 'verified', 'houselicenses', 'customertypeid', 'lasturl', 'login' ]);
        //$fields = Schema::getColumnListing($customer->getTable());
        $vattr = (new ValidationAttributes($customer))->setCast('notes', 'textarea');
        return view('myaccount/registration', ['models' => [$customer], 'fields' => $fields, 'vattr' => $vattr]);
    }

    public function updateregistration()
    {
        //Todo: Handle change and check of email address.
        if (!Auth::check()) return redirect('/login');
        $customer = Customer::Find(Input::get('id'));

        $fields = array_diff(Schema::getColumnListing($customer->getTable()), ['id', 'created_at', 'updated_at', 'remember_token', 'plain_password', 'password', 'ownerid', 'status', 'verified', 'houselicenses', 'customertypeid', 'lasturl', 'login' ]);
        foreach ($fields as $field)
        {
            $customer->$field = Input::get($field);
        }

        //We save. The save validates after the Mutators have been used.
        $errors = '';
        $success = 'Your contact data have been updated';
        if (!$customer->save()) {
            $errors = $customer->getErrors();
            $success = '';
        }
        if ($errors != '') return redirect('/customer/registration')->with('success', $success)->with('errors',$errors)->withInput(Input::except('plain_password'));
        return redirect('/myaccount/registration?menupoint=9010')->with('success', 'Customer has been updated!');
    }

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

    public function listbookings()
    {
        return view('myaccount/listbookings', ['models' => $this->getContracts()]);
    }

    public function listaccountposts()
    {
        return view('myaccount/listaccountposts', ['models' => $this->getContracts()]);
    }

    public function listmails()
    {
        if (!Auth::check()) return redirect('/login');
        $user = Auth::user();
        $emails = Emaillog::where('to', $user->email)->where('customerid', $user->id)->get();
        return view('myaccount/listmails', ['models' => $emails]);
    }

    public function edittime()
    {
        $contracts = $this->getContracts(Carbon::now());
        $contract = $contracts[0];
        return view('myaccount/edittime', ['models' => $contracts, 'vattr' => new ValidationAttributes($contract)]);
    }

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
            if ($landingdatetime == '') $landingdatetime = $contractoverview->from->format('Y-m-d');
            $departuredatetime = Input::get('departuredatetime_'.$contract->id);
            if ($departuredatetime == '') $departuredatetime = $contractoverview->to->format('Y-m-d');

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
