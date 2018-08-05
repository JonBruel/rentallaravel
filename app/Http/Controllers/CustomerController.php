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
    public function index(Request $request)
    {

        if (Gate::allows('Owner')) {
            $customers = $this->model::filter($request->all())->sortable('id')->paginate(10);

            $params['edit'] = "?menupoint=1020&".session('querystring');
            $params['show'] = "?menupoint=1030&".session('querystring');

            return view('customer/index', ['models' => $customers, 'params' => $params, 'search' => $request->all()]);
        }
        else {
            $request>session()->flash('warning', 'You are now allowed to see the customer list.');
            return redirect('home');
        }

    }

    public function hashpasswords()
    {
        //Password hash
        $customers = $this->model::all();
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
        $models = [new $this->model()];
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
    public function store(Request $request)
    {
        $model = new $this->model();

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
        foreach ($fields as $field) $model->$field = $request->get($field);

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
    public function show(Request $request, $id)
    {
        //Find page from id
        if ($request->query('page') == null) {
            $models = $this->model::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            $request->merge(['page' => $page]);
        }

        $models = $this->model::filter(Input::all())->sortable('id')->paginate(1);
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
            $models = $this->model::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
        }

        $models = $this->model::filter(Input::all())->sortable('id')->paginate(1);
        $fields = Schema::getColumnListing($models[0]->getTable());
        return view('customer/edit', ['models' => $models, 'fields' => $fields, 'vattr' => new ValidationAttributes($models[0])]);
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
        $model = (new $this->model)->findOrFail($id);
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
    public function destroy(Request $request, $id)
    {
        $toBeDeleted = (new $this->model)->findOrFail($id);
        $name = $toBeDeleted->name;
        $toBeDeleted->delete();
        return redirect('/customer/index?menupoint=1010')->with('success', 'Customer ' . $name . ' has been deleted!');
    }
}
