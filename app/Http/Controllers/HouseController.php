<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\House;
use Schema;
use Kyslik\ColumnSortable\Sortable;
use App\Rules\Name;

class HouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = House::sortable()->paginate(10);
        return view('house/index', ['models' => $models]);
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
        $model = (new House)->findOrFail($id);
        $fields = Schema::getColumnListing($model->getTable());
        return view('house/show', ['model' => $model, 'fields' => $fields]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = (new house)->findOrFail($id);
        $fields = Schema::getColumnListing($model->getTable());
        return view('house/edit', ['model' => $model, 'fields' => $fields]);
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
        $modelOriginal = (new House)->findOrFail($id);
        $fields = Schema::getColumnListing($modelOriginal->getTable());

        $model = new House();
        $data = $this->validate($request, ['longitude' => 'required',
            'name' => ['required', 'string', new Name]]);

        $data['id'] = $id;
        foreach ($fields as $field){
            if (array_key_exists($field, $data)) $modelOriginal->$field = $data[$field] ;
        }
        //die($data['longitude']);
        $modelOriginal->save();
        return redirect('/houses')->with('success', 'House has been updated!');
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
