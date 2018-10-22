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
use App\Models\Customertype;
use App\Models\Emaillog;
use App\Models\Batchlog;
use App\Models\Accountpost;
use App\Models\Contract;
use App\Models\Customer;

//$relatedmodels = [Emaillog::class, Batchlog::class, Accountpost::class, Contract::class];

/**
 * Class CustomerController
 * @package App\Http\Controllers
 */
class CustomerController extends Controller
{

    public function __construct() {
        parent::__construct(\App\Models\Customer::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!Gate::allows('Owner')) return redirect('home')->with('warning', 'You are now allowed to see the customer list.');

        $customers = Customer::filter(Input::all())->where('status', 1)->sortable('id')->with('accountposts')->paginate(10);

        $customertypeselect = ['' => __('All')]+Customertype::where('id', '>', Auth::user()->customertypeid)->pluck('customertype', 'id')
                ->map(function ($item, $key) {return $item = __($item);} )
                ->toArray();

        //Check if customers are allowed to be deleted
        $allowdeletes = [];
        foreach($customers as $customer)
        {
            $allowdeletes[$customer->id] = false;
            $accountposts = $customer->accountposts();
            $count = $accountposts->where('posttypeid', 10)->count();
            $allowdeletes[$customer->id] = ($count == 0);

            //Special cases:
            if ($customer->customertypeid <= 10) $allowdeletes[$customer->id] = false;
            if ($customer->id == 10) $allowdeletes[$customer->id] = false;
        }

        return view('customer/index', ['models' => $customers,  'search' => Input::all(), 'customertypeselect' => $customertypeselect, 'allowdeletes' => $allowdeletes]);

    }

    public function hashpasswords()
    {
        //Password hash
        $customers = Customer::all();
        foreach ($customers as $customer)
        {
            if ((strlen($customer->password < 20) and (strlen($customer->password) > 3)))
            {
                $customer->password = Hash::make($customer->password);
                $customer->save();
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $models = [new Customer()];
        $fields = [
            'name',
            'address1',
            'address2',
            'country',
            'telephone',
            'mobile',
            'email',
            'login',
            'plain_password'
        ];
        return view('customer/create', ['models' => $models, 'fields' => $fields, 'vattr' => new ValidationAttributes($models[0])]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $model = new Customer();

        $fields = [
            'name',
            'address1',
            'address2',
            'country',
            'telephone',
            'mobile',
            'email',
            'login',
            'plain_password'
        ];
        foreach ($fields as $field) $model->$field = Input::get($field);

        //Set structural fields
        //$model->ownerid = Auth::user()->ownerid;
        $model->ownerid = 1;
        $model->password = Hash::make(Input::get('plain_password'));
        $model->houselicenses = 0;
        $model->customertypeid = 1000;
        $model->status = 1;
        $model->cultureid = 1;

        //We save. The save validates after the Mutators have been used.
        //$errors = '';
        $errors = new MessageBag();
        $success = 'Customer has been updated!';
        if (!$model->save()) {
            $errors = $model->getErrors();
            $success = 'Customer was not updated!';
        }
        if ($errors->any()) return redirect('/customer/create')->with('success', $success)->with('errors',$errors)->withInput(Input::except('plain_password'));
        return redirect('/customer/index?menupoint=1010')->with('success', $success);
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
            $models = Customer::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
        }

        $models = Customer::filter(Input::all())->sortable('id')->paginate(1);
        $fields = Schema::getColumnListing($models[0]->getTable());
        return view('customer/show', ['models' => $models, 'fields' => $fields]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Find page from id
        if (Input::get('page') == null) {
            $models = Customer::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
        }

        $models = Customer::filter(Input::all())->sortable('id')->paginate(1);
        $fields = array_diff(Schema::getColumnListing($models[0]->getTable()), ['created_at', 'updated_at', 'remember_token', 'plain_password']);
        return view('customer/edit', ['models' => $models, 'fields' => $fields, 'vattr' => (new ValidationAttributes($models[0]))->setCast('notes', 'textarea')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $model =  Customer::findOrFail($id);
        $fields = Schema::getColumnListing($model->getTable());

        foreach ($fields as $field){
            $model->$field = Input::get($field);
            if ($field == 'password' and strlen(Input::get('password') < 60)) $model->$field = Hash::make(Input::get('password'));
            //if ($field == 'cultureid') die("Cultureid: " . Input::get('cultureid'));
        }
        //We save. The save validates after the Mutators have been used.
        $errors = '';
        $success = 'House has been updated!';
        if (!$model->save()) {
            $errors = $model->getErrors();
            $success = '';
        }
        if ($errors != '') return redirect('/customer/edit/'.$id)->with('success', $success)->with('errors',$errors)->withInput(Input::except('plain_password'));
        return redirect('/customer/index?menupoint=1010')->with('success', 'Customer has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $toBeDeleted = (new Customer)->findOrFail($id);
        $name = $toBeDeleted->name;
        $toBeDeleted->delete();
        return redirect('/customer/index?menupoint=1010')->with('success', 'Customer ' . $name . ' has been deleted!');
    }

    public function statistics()
    {
        /*
         *  # Set your primary language.
            # Possible value:
            # Albanian=al, Bosnian=ba, Bulgarian=bg,
            # Chinese (Taiwan)=tw, Chinese (Simpliefied)=cn, Czech=cz,
            # Danish=dk, Dutch=nl, English=en, Estonian=et, Finnish=fi, French=fr,
            # German=de, Greek=gr, Hebrew=he, Hungarian=hu, Indonesian=id, Italian=it,
            # Japanese=jp, Korean=kr, Latvian=lv, Norwegian (Nynorsk)=nn,
            # Norwegian (Bokmal)=nb, Polish=pl, Portuguese=pt, Portuguese (Brazilian)=br,
            # Romanian=ro, Russian=ru, Serbian=sr, Slovak=sk, Spanish=es,
            # Spanish (Catalan)=es_cat, Swedish=se, Turkish=tr, Ukrainian=ua, Welsh=wlk.
            # First available language accepted by browser=auto
            # Default: "auto"
         */

        $awlanguage = substr(\App::getLocale(),0,2);
        if ($awlanguage == 'da') $awlanguage = 'dk';
        //TODO: make general
        $url = 'http://cangeroni.hasselbalch.com';
        $awurl = "http://awstat.consiglia.dk/awstats/awstats.pl?lang=".$awlanguage."&config=" . substr($url,7);
        return view('customer/statistics', ['awurl' => $awurl]);
    }

    public function tools($id)
    {
        return view('customer/tools', ['id' => $id]);
    }

    public function merge($id = 0)
    {
        $input1 = Input::get('input1', []);
        $input2 = Input::get('input2', []);

        if ($id != 0)
        {
            $input1['from'] = $id;
        }
        $success = '';
        if (Input::get('action') == 'merge')
        {
            $id1 = (array_key_exists('from', $input1))?$input1['from']:false;
            $id2 = (array_key_exists('to', $input2))?$input2['to']:false;
            if (($id1) && ($id2)) $this->domerge($id1, $id2);
            else $success = __('From or to not ticked off.');
        }
        $customers1 = Customer::filter($input1)->paginate(10);
        if ($id != 0) $customers1 = Customer::where('id', $id)->paginate();
        $customers2 = Customer::filter($input2)->paginate(10);
        session()->flash('warning', $success);
        return view('customer/merge', ['customers1' => $customers1, 'customers2' => $customers2, 'input1' => $input1, 'input2' => $input2]);
    }

    public function domerge($tobedeleted, $remaining)
    {
        $relatedmodels = [Emaillog::class, Batchlog::class, Accountpost::class, Contract::class];
        foreach($relatedmodels as $relatedmodel)
        {
            foreach ($relatedmodel::where('customerid', $tobedeleted)->get() as $model)
            {
                $model->customerid = $remaining;
                $model->save();
            }

        }
        foreach (Accountpost::where('postedbyid', $tobedeleted)->get() as $model)
        {
            $model->postedbyid = $remaining;
            $model->save();
        }
        Customer::Find($tobedeleted)->delete();
        return;
    }

    public function checkaccount($id)
    {
        $customername = Customer::Find($id)->name;
        $accountposts = Accountpost::where('customerid', $id)->orderBy('contractid')->orderBy('created_at')->get();
        return view('customer/listaccountposts', ['models' => $accountposts, 'customername' => $customername]);
    }

}
