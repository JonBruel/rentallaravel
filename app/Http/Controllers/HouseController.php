<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\House as Model;
use Schema;
use Gate;
use Kyslik\ColumnSortable\Sortable;
use App\_Rules_not_used_anymore\Name;
use ValidationAttributes;
use App\Models\Customer;
use App\Models\Culture;
use App\Models\House;
use App\Models\HouseI18n;
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
    public function listhouses()
    {
        $models = $this->model::sortable()->filter(Input::all())->paginate(10);
        $owners = ['' => __('Please select owner')] + Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
        $sortparams = (Input::query('order'))?'&order='.Input::query('order'):'';
        $sortparams .= (Input::query('sort'))?'&sort='.Input::query('sort'):'';

        $params['edit'] = "?menupoint=12030";
        $params['edit'] .= $sortparams;

        $params['show'] = "?menupoint=12030";
        $params['show'] .= $sortparams;

        if (sizeof($models) == 1)
        {
            return redirect('house/edit/'.$models[0]->id);
        }

        return view('house/index', ['models' => $models, 'params' => $params, 'search' => Input::all(), 'owners' => $owners]);
    }

    public function checkAccess($id)
    {
        $house = House::filter()->where('id', $id)->first();
        if (!$house) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        return $house;
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

        $fields = array_diff(Schema::getColumnListing($model->getTable()), ['created_at', 'updated_at', 'lockbatch', 'viewfilter']);
        $vattr = (new ValidationAttributes($model))->setCast('id', 'hidden');
        $housefields = $model->toArray();
        $googlekey = config('app.googlekey', 'AIzaSyCmXZ5CEhhFY3-qXoHRzs0XFK4a495LyxE');
        return view('house/edit', ['models' => [$model], 'fields' => $fields, 'vattr' => $vattr, 'administrator' => Gate::allows('administrator'), 'housefields' => json_encode($housefields), 'googlekey' => $googlekey]);
    }

    public function edithousehtml($id)
    {
        $cultures = Culture::where('culturename', '<>', 'Test')->get();

        $languages = [];
        foreach($cultures as $culture) $languages[$culture->culture] = $culture->culturename;

        $houseI18ns = [];
        foreach ($cultures as $culture)
        {
            //die('Culture: '.$culture->culture);
            $houseI18n = HouseI18n::where('id', $id)->where('culture', $culture->culture)->first();
            if (!$houseI18n)
            {
                $houseI18n = new HouseI18n();
                $houseI18n->id = $id;
                $houseI18n->culture = $culture;
                $houseI18n->save();
            }
            if (strpos($houseI18n->environment, 'inseeto') !== false)
            {
                $houseI18n->environment = '';
                $houseI18n->save();
            }
            $houseI18ns[$culture->culture] = $houseI18n;
        }

        $fields = array_diff(Schema::getColumnListing($houseI18n->getTable()), ['culture', 'id', 'created_at', 'updated_at']);
        $vattr = (new ValidationAttributes($houseI18n))->setCast('id', 'hidden')->setCast('culture', 'hidden');

        $fieldselect = [];
        foreach ($fields as $field)
        {
            $vattr->setCast($field, 'textarea');
            $fieldselect[$field] = $field;
        }

        $culturenames = [];
        foreach ($cultures as $culture) $culturenames[] = $culture->culture;

        $nontinymcefields = array_diff($fields, ['seo','keywords']);

        //TODO: Test, remove it
        //$culturenames = ['da_DK'];
        $field = Input::get('field', 'description');
        $fields = [$field];

        //TODO: use webroot function to make it general
        $uploaddirdocuments = '/var/www/html/rentallaravel/public/housedocuments/'.$id.'/';
        $uploaddirgraphics =  '/var/www/html/rentallaravel/public/housegraphics/'.$id.'/';
        $uploaddirgallery =   '/var/www/html/rentallaravel/public/housegraphics/'.$id.'/gallery1/';

        return view('house/edithousehtml', ['models' => $houseI18ns, 'model' => $houseI18n,
            'fields' => $fields,
            'field' => $field,
            'nontinymcefields' => $nontinymcefields,
            'fieldselect' => $fieldselect,
            'vattr' => $vattr,
            'administrator' => Gate::allows('administrator'),
            'cultures' => $culturenames,
            'languages' => $languages,
            'uploaddirdocuments' => $uploaddirdocuments,
            'uploaddirgraphics' => $uploaddirgraphics,
            'uploaddirgallery' => $uploaddirgallery,
            'id' => $id]);
    }

    public function updatehousehtml($id)
    {
        $chosenfield = Input::get('field');
        //We save. The save validates after the Mutators have been used.
        $errors = '';
        $success = __(ucfirst($chosenfield)).' '.__('has been updated').'!';
        foreach (Input::get($chosenfield) as $culture => $value)
        {
            $I18n = HouseI18n::where('id', $id)->where('culture', $culture)->first();
            $I18n->$chosenfield = $value;
            if (!$I18n->save())
            {
                $errors = $I18n->getErrors();
                $success = '';
            }
        }
        return redirect('house/edithousehtml/'.$id.'?field='.$chosenfield)->with('success', $success)->with('errors',$errors);
    }

    public function browse($id)
    {
        $house = $this->checkAccess($id);

        $myfiles = [];
        $uploaddir = [];
        $uploaddir['Housegraphics'] = public_path().'/housegraphics/' . $id . '/';
        $uploaddir['Housedocuments'] = public_path().'/housedocuments/' . $id . '/';
        $uploaddir['Gallery'] = public_path().'/housegraphics/' . $id . '/gallery1/';

        foreach ($uploaddir as $key => $dir)
        {
            if (file_exists($dir)) $myfiles[$key] = scandir($dir);
            else $myfiles[$key] = [__('No files have been uploaded')];
        }

        return view('house/browse', ['myfiles' => $myfiles, 'id' => $id, 'housename' => $house->name]);
    }

    public function deletefiles($id)
    {
        $this->checkAccess($id);

        $uploaddir = [];
        $uploaddir['Housegraphics'] = public_path().'/housegraphics/' . $id . '/';
        $uploaddir['Housedocuments'] = public_path().'/housedocuments/' . $id . '/';
        $uploaddir['Gallery'] = public_path().'/housegraphics/' . $id . '/gallery1/';

        //We delete if asked for
        $file = Input::get('file', []);
        foreach ($file as $key => $value)
        {
            $filecomponents = explode(';', $value);
            $filename =  $uploaddir[$filecomponents[0]].$filecomponents[1];
            unlink($filename);
        }

        return redirect('house/browse/'.$id)->with('success', $filename.' '.__('deleted'));
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
