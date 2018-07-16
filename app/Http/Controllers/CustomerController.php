<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Schema;
use Gate;
use ValidationAttributes;

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
        //$this->authorize('administrator');
        if (Gate::allows('administrator')) {
            $customers = $this->model::filter()->sortable()->paginate(10);

            $sortparams = ($request->query('order'))?'&order='.$request->query('order'):'';
            $sortparams .= ($request->query('sort'))?'&sort='.$request->query('sort'):'';

            $params['edit'] = "?menupoint=1020";
            $params['edit'] .= $sortparams;

            $params['show'] = "?menupoint=1030";
            $params['show'] .= $sortparams;

            return view('customer/index', ['models' => $customers, 'params' => $params]);
        }
        else {
            $request>session()->flash('warning', 'You are now allowed to see the customer list.');
            return redirect('customer/index');
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
    public function show(Request $request, $id)
    {
        //Find page from id
        if ($request->query('page') == null) {
            $models = $this->model::sortable()->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            $request->merge(['page' => $page]);
        }

        $models = $this->model::sortable()->paginate(1);
        $fields = Schema::getColumnListing($models[0]->getTable());
        return view('customer/show', ['models' => $models, 'fields' => $fields]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //Find page from id
        if ($request->query('page') == null) {
            $models = $this->model::sortable()->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            $request->merge(['page' => $page]);
        }

        $models = $this->model::sortable()->paginate(1);
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
    public function update(Request $request, $id)
    {
        $model = (new $this->model)->findOrFail($id);
        $fields = Schema::getColumnListing($model->getTable());

        foreach ($fields as $field){
            $model->$field = $request->get($field) ;
        }
        //We save. The save validates after the Mutators have been used.
        $errors = '';
        $success = 'House has been updated!';
        if (!$model->save()) {
            $errors = $model->getErrors();
            $success = '';
        }
        if ($errors != '') return redirect('/customer/edit/'.$id)->with('success', $success)->with('errors',$errors);
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
        $toBeDeleted = (new $this->model)->findOrFail($id);
        $name = $toBeDeleted->name;
        $toBeDeleted->delete();
        return redirect('/customer/index?menupoint=1010')->with('success', 'Customer ' . $name . ' has been deleted!');
    }
}
