<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Schema;
use Gate;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$this->authorize('administrator');
        if (Gate::allows('administrator')) {
            $customers = Customer::sortable()->paginate(10);
            return view('customer/index', ['models' => $customers]);
        }
        else {
            $request>session()->flash('warning', 'You are now allowed to see the customer list.');
            return redirect('/houses');
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = (new Customer)->findOrFail($id);
        $fields = Schema::getColumnListing($model->getTable());
        return view('customer/show', ['model' => $model, 'fields' => $fields]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = (new Customer)->findOrFail($id);
        $fields = Schema::getColumnListing($model->getTable());
        return view('customer/edit', ['model' => $model, 'fields' => $fields]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelOriginal = (new Customer)->findOrFail($id);
        $fields = Schema::getColumnListing($modelOriginal->getTable());

        $customer = new Customer();
        $data = $this->validate($request, ['address3' => 'required']);

        $data['id'] = $id;
        foreach ($fields as $field){
            if (array_key_exists($field, $data)) $modelOriginal->$field = $data[$field] ;
        }
        $modelOriginal->save();
        return redirect('/customers')->with('success', 'Customer has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
