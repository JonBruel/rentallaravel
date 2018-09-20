<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\House as Model;
use Schema;
use Kyslik\ColumnSortable\Sortable;
use App\_Rules_not_used_anymore\Name;
use ValidationAttributes;
use App\Models\Customer;
use Illuminate\Support\Facades\Input;


class HouseController extends Controller
{

    public function __construct() {
        parent::__construct(\App\Models\House::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = $this->model::sortable()->filter(Input::all())->paginate(10);
        $owners = ['' => __('Please select owner')] + Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
        $sortparams = (Input::query('order'))?'&order='.Input::query('order'):'';
        $sortparams .= (Input::query('sort'))?'&sort='.Input::query('sort'):'';

        $params['edit'] = "?menupoint=2120";
        $params['edit'] .= $sortparams;

        $params['show'] = "?menupoint=2130";
        $params['show'] .= $sortparams;

        return view('house/index', ['models' => $models, 'params' => $params, 'search' => Input::all(), 'owners' => $owners]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //Find page from id
        if (Input::query('page') == null) {
            $models = $this->model::sortable()->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
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
    public function edit($id)
    {
        $model = (new $this->model)->findOrFail($id);

        $fields = Schema::getColumnListing($model->getTable());
        return view('house/edit', ['model' => $model, 'fields' => $fields, 'vattr' => new ValidationAttributes($model)]);
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

        //We set the model
        $fields = Schema::getColumnListing($model->getTable());
        foreach ($fields as $field){
            $model->$field = Input::get($field) ;
        }

        //We save. The save validates after the Mutators have been used.
        $errors = '';
        $success = 'House has been updated!';
        if (!$model->save()) {
            $errors = $model->getErrors();
            $success = '';
        }

        return redirect('/house/edit/'.$id)->with('success', $success)->with('errors',$errors);
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

    public function listperiods()
    {

    }
}
