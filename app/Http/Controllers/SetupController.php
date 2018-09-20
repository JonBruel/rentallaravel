<?php

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
use App\Models\Batchtask;
use App\Models\Customer;
use App\Models\House;
use App\Models\Standardemail;
use App\Models\StandardemailI18n;
use App\Models\Period;
use App\Models\Periodcontract;
use App\Models\Culture;
use App;
use App\Models\Contractoverview;
use Carbon\Carbon;

class SetupController extends Controller
{

    public function __construct() {
        parent::__construct(\App\Models\Batchtask::class);
    }

    public function listbatchtasks()
    {
        $models = Batchtask::filter(Input::all())->orderBy('id')->get();

        $owners = ['' => __('Please select owner')] + Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
        $houses = ['' => __('Please select house')] + House::filter(Input::all())->pluck('name', 'id')->toArray();

        return view('/setup/listbatchtasks', ['models' => $models, 'houses' => $houses, 'owners' => $owners, 'ownerid' => Input::get('ownerid', ''), 'search' => Input::all()]);
    }

    public function editbatchtask($id)
    {
        //Find page from id
        if (Input::get('page') == null) {
            $models = $this->model::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
        }

        $models = $this->model::filter(Input::all())->sortable('id')->paginate(1);

        $lockbatch = $models[0]->house->lockbatch;
        $lockbatchpossibilities = [ 0 => __('Batchtask system activated'),
                                    1 => __('Batchtask system activated as usual but NOT updated. Only test mails are dispatched.'),
                                    2 => __('The queue is updated, no mails are send, maillog is NOT updated, but the queue status is updated.'),
                                    3 => __('Batchtask system deactivated.')];

        $fields = Schema::getColumnListing($models[0]->getTable());
        return view('setup/editbatchtask', ['models' => $models,
            'lockbatch' => $lockbatch,
            'lockbatchpossibilities' => $lockbatchpossibilities,
            'vattr' => new ValidationAttributes($models[0])]);
    }

    public function updatebatchtask($id)
    {
        $original = $this->model::Find($id);

        //Lock batchtask
        $original->house->lockbatch = Input::get('lockbatch', 3);
        $original->house->save();


        //Fields with "normal" values
        $fields1 = ['name', 'activefrom', 'posttypeid', 'emailid', 'batchfunctionid', 'paymentbelow', 'requiredposttypeid',
                    'timedelaystart', 'timedelayfrom', 'addposttypeid', 'dontfireifposttypeid'];

        //Yes/no fields
        $fields2 = ['usepaymentbelow', 'userequiredposttypeid', 'usetimedelaystart', 'usetimedelayfrom',
                    'useaddposttypeid', 'usedontfireifposttypeid', 'active'];

        //Multiple yes/no fields
        $fields3 = ['mailto'];

        foreach($fields1 as $field) $original->$field = Input::get($field);
        foreach($fields2 as $field) $original->$field = Input::get($field,0);
        foreach($fields3 as $field) $original->$field = implode(',', Input::get($field, []));

        $errors = '';
        $success = 'Batcktask has been updated!';
        if (!$original->save()) {
            $errors = $original->getErrors();
            $success = '';
        }

        return redirect('/setup/editbatchtask/'.$id.'?ownerid='.$original->ownerid)->with('success', $success)->with('errors', $errors);
    }

    //liststandardemails, editstandardemails, updatestandardemails
    public function liststandardemails()
    {
        $models = Standardemail::filter(Input::all())->orderBy('id')->get();

        $owners = ['' => __('Please select owner')] + Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
        $houses = ['' => __('Please select house')] + House::filter(Input::all())->pluck('name', 'id')->toArray();

        return view('/setup/liststandardemails', ['models' => $models, 'houses' => $houses, 'owners' => $owners, 'ownerid' => Input::get('ownerid', ''), 'search' => Input::all()]);
    }

    public function editstandardemail($id)
    {
        //Find page from id
        if (Input::get('page') == null) {
            $models = Standardemail::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
        }

        $models = Standardemail::filter(Input::all())->sortable('id')->paginate(1);

        //Get email text
        $standardemailcontents = [];
        $cultures = explode(';', config('app.cultures'));
       // die(config('app.cultures'));
        foreach ($cultures as $culture) {
            $standardemail = StandardemailI18n::where('id', $models[0]->id)->where('culture', $culture)->first();
            $standardemailcontents[$culture] = ($standardemail)?$standardemail->contents:'';
        }
        $values = $models[0]->toArray();
        $new = new StandardemailI18N($values);



        return view('setup/editstandardemail', ['models' => $models,
            'vattr' => new ValidationAttributes($models[0]),
            'standardemailcontents' => $standardemailcontents]);
    }

    public function updatestandardemail($id)
    {
        $errors = '';
        $success = 'Standardemail has been updated!';
        $keys = '';

        //Update displayed cultere dependent contents
        $updatedcultures = [];
        foreach(Input::get('contents', []) as $key => $contents)
        {
            //Check if key exists, if not create the record
            $i18n = StandardemailI18N::where('id', $id)->where('culture', $key)->first();
            if (!$i18n) $i18n = new StandardemailI18N();

            //Update and save the record
            $i18n->contents = $contents;
            $i18n->culture = $key;
            $i18n->id = $id;
            //TODO: Check handling of error
            if (!$i18n->save())
            {
                $errors = $i18n->getErrors();
                $success = '';
            }
            $updatedcultures[] = $key;
        }

        $original = Standardemail::Find($id);
        $original->description = Input::get('description');
        if (!$original->save()) {
            $errors = $original->getErrors();
            $success = '';
        }

        return redirect('/setup/editstandardemail/'.$id.'?ownerid='.$original->ownerid)->with('success', $success)->with('errors', $errors);
    }

    /*
     * This method is the step for making new batch tasks for existing houses. The idea is
     * that it is too complicated to start from scratch, so the ovwe or supervisor is
     * suggested to copy existing batch task from the tasks belonging to the house  with id=0.
     */
    public function makebatch1()
    {
        $houses = House::filter()->sortable('id')->where('id', '>', 0)->paginate(25);
        $batchexistss = [];
        foreach ($houses as $house)
        {
            $id = $house->id;
            $batchexistss[$id] = 0;
            if ((Batchtask::where('houseid', $id)->first()) && (Standardemail::where('houseid', $id))->first()) $batchexistss[$id] = 1;
        }
        return view('/setup/makebatch1', ['houses' => $houses, 'batchexistss' => $batchexistss, 'search' => Input::all()]);
    }

    //The function copies the batchtasks and standardemails belonging to house with id = 0
    //to the houses filtered to
    //Both tables have set up an unique key name (or description), houseid
    //This is used as a way to update the records without having to delete them first.
    //We want to avoid deletion due to foreign key contraints....
    public function copybatch($houseid, $overwrite, $batchexists)
    {
        $goahead = false;
        $success = __('Nothing done');
        if ((Input::get('answer', 'no') == 'yes') && ($houseid > 0)) $goahead = true;
        if ($goahead)
        {
            House::copyBatchAndMail($houseid, $overwrite);
            $success = __('Batch system updated');
        }
        if ((Input::get('answer', 'nothing') == 'nothing') && ($houseid > 0)) return view('setup/copybatch', ['houseid' => $houseid, 'overwrite' => $overwrite, 'batchexists' => $batchexists]);
        return redirect('setup/makebatch1')->with('success', $success);
    }
}
