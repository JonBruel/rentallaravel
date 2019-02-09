<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\House as Model;
use Schema;
use Gate;
use Kyslik\ColumnSortable\Sortable;
use ValidationAttributes;
use App\Models\Customer;
use App\Models\Culture;
use App\Models\Period;
use App\Models\Periodcontract;
use App\Models\House;
use App\Models\HouseI18n;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;

/**
 * Class HouseController
 * @package App\Http\Controllers
 */
class HouseController extends Controller
{

    public function __construct() {
        parent::__construct(\App\Models\House::class);
    }

    /**
     * List houses using filter and sorter. The scope depends on the ownership relations with the user. If there is only one
     * house the user will be redirected to house/edit.
     *
     * @return \Illuminate\Http\Response
     */
    public function listhouses()
    {
        $models = House::sortable()->filter(Input::all())->paginate(10);
        $owners = ['' => __('Please select owner')] + Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
        $sortparams = (Input::query('order'))?'&order='.Input::query('order'):'';
        $sortparams .= (Input::query('sort'))?'&sort='.Input::query('sort'):'';

        if (sizeof($models) == 1)
        {
            return redirect('house/edit/'.$models[0]->id);
        }

        return view('house/listhouses', ['models' => $models, 'search' => Input::all(), 'owners' => $owners]);
    }

    /**
     * A helper function which uses the filter to check that the user has the right to see that house.
     * This may not be the case if the house belong to another site.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkAccess($id)
    {
        $house = House::filter()->where('id', $id)->first();
        if (!$house) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        return $house;
    }

    /**
     * Feeds the view for editing specific information about the house, things which are not translated.
     *
     * @param  int  $id house id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $model = (new House)->findOrFail($id);

        $fields = array_diff(Schema::getColumnListing($model->getTable()), ['created_at', 'updated_at', 'lockbatch', 'viewfilter', 'isprivate']);
        $vattr = (new ValidationAttributes($model))->setCast('id', 'hidden');
        $housefields = $model->toArray();
        $googlekey = config('app.googlekey', 'AIzaSyCmXZ5CEhhFY3-qXoHRzs0XFK4a495LyxE');
        return view('house/edit', ['models' => [$model], 'fields' => $fields, 'vattr' => $vattr, 'administrator' => Gate::allows('administrator'), 'housefields' => json_encode($housefields), 'googlekey' => $googlekey]);
    }

    /**
     * Feeds the view to edit things which are translated into different languages.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edithousehtml($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
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

        $fields = array_diff(Schema::getColumnListing($houseI18n->getTable()), ['culture', 'id', 'created_at', 'updated_at', 'isprivate']);
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

        $uploaddirdocuments = public_path().'/housedocuments/'.$id.'/';
        $uploaddirgraphics =  public_path().'/housegraphics/'.$id.'/';
        $uploaddirgallery =   public_path().'/housegraphics/'.$id.'/gallery1/';

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

    /**
     * Updates information about the house which is translated into different languages.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatehousehtml($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
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

    /**
     * Feeds the view where all images for descriptions, gallery and documents can be seen. Uploading of these takes place in the
     * edithousehtml view. So the purpose of this view is overview and the possibility to delete files.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function browse($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
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

    /**
     * As a reult of the browse function this function deletes one or several files
     *
     * @param $id of house where a file name, as given in the parameter name, to de deleted
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletefiles($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
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
     * Updates the basic information about a house or creates a new house based on values of an existing house.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $success = __('House has been updated!');
        if (Input::get('Newhouse'))
        {
            $model = new House();
            $success = __('A new house has been created!');
        }
        else $model = House::findOrFail($id);

        //We set the model
        $fields = Schema::getColumnListing($model->getTable());
        foreach ($fields as $field){
            if ($field != 'id') $model->$field = Input::get($field) ;
        }
        $model->viewfilter = 1000;

        //We save. The save validates after the Mutators have been used.
        $errors = '';
        if (!$model->save()) {
            $errors = $model->getErrors();
            $success = '';
        }

        return redirect('/house/edit/'.$id)->with('success', $success)->with('errors',$errors);
    }

    /**
     * Deletes a house.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));

        House::Find($id)->delete();
        return redirect('/house/listhouses?menupoint=12030');
    }


    /**
     * Method is used for the owner to get an overview off all periods and as an entry to change
     * the price for one or several periods. There is no pre-selection of the houseid.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function listperiods()
    {
        if (!Gate::allows('Administrator')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $year = Carbon::now()->year;
        $search = Input::all();
        $models = Periodcontract::filter(Input::all())->orderBy('from')->whereDate('from', '>', Carbon::createFromDate($year,1,1))->with('house')->paginate(26);
        $houses = ['' => __('Please select house')] + House::filter(Input::all())->pluck('name', 'id')->toArray();

        $fields = ['from', 'to'];
        return view('house/listperiods', ['models' => $models, 'fields' => $fields, 'houses' => $houses, 'search' => $search]);
    }

    /**
     * Used to show the values of the chosen rental period for a subsequent change of the details.
     *
     * @param $id id of the period
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editperiod($id)
    {
        if (!Gate::allows('Administrator')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $model = Period::Find($id);
        $fields = ['id', 'baseprice','basepersons', 'maxpersons','personprice'];
        $vattr = (new ValidationAttributes($model))->setCast('id', 'hidden');
        return view('house/editperiod', ['models' => [$model], 'fields' => $fields, 'vattr' => $vattr]);
    }

    /**
     * Used to change details of a period.
     *
     * @param $id of the period
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateperiod($id)
    {
        if (!Gate::allows('Administrator')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $model = Period::Find($id);
        $fields = ['baseprice','basepersons', 'maxpersons','personprice'];

        foreach($fields as $field)
        {
            $model->$field = Input::get($field);
        }
        $success = __('Period updated');
        $errors = [];
        if (!$model->save())
        {
            $errors = $model->getErrors();
            $success = '';
        }
        return redirect('house/editperiod/'.$id)->with('success', $success)->with('errors', $errors);
    }

    /**
     * Not implemented
     */
    public function updateperiods()
    {

    }

    /**
     * Prepares the view for inseting a several ranges of periods, each range belong to a season
     * where the user defines the price and other details.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function createperiods()
    {
        //echo('test:'.Input::get('test', 'nothing') );
        //die(var_dump($request->get('data')));
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        if (Input::get('test') == 'yes') return $this->insertcreatedperiods();

        $houses = House::filter(Input::all())->pluck('name', 'id')->toArray();
        $seasons = Input::get('seasons', 5);
        $periodlength = Input::get('periodlength', 7);
        $houseid = Input::get('houseid', 1);
        $thisyear = Carbon::now()->year;

        //Find last 5 period ends
        $periods = Period::where('houseid', $houseid)->orderBy('to', 'desc')->limit(5)->get();
        $lastperiods = '';
        foreach ($periods as $key => $period) {
            if ($key == 0) $lastperiods = $period->to->formatLocalized('%a %d %b %Y').'.';
            else $lastperiods = $period->to->formatLocalized('%a %d %b %Y') . ', ' . $lastperiods;
        }

        //Initialize raw example data
        if (Input::get('test', 'no') == 'no')
        {
            $data = [];
            for($i=0;$i<$seasons;$i++)
            {
                $data['seasonstart'][$i] = '';
                $data['seasonend'][$i] = '';
                $data['basepersons'][$i] = '';
                $data['maxpersons'][$i] = '';
                $data['baseprice'][$i] = '';
                $data['personprice'][$i] = '';
                if (sizeof($houses) == 1)
                {
                    $lastperiod = Period::where('houseid', $houseid)->orderBy('to', 'desc')->first();
                    if ($lastperiod)
                    {
                        $data['seasonstart'][$i] = $lastperiod->to->format('d-m-Y');
                        $data['seasonend'][$i] = $data['seasonstart'][$i];
                        $data['basepersons'][$i] = $lastperiod->basepersons;
                        $data['maxpersons'][$i] = $lastperiod->maxpersons;
                        $data['baseprice'][$i] = $lastperiod->baseprice;
                        $data['personprice'][$i] = $lastperiod->personprice;
                    }
                }
            }
        }
        if (Input::get('test') == 'redirected') $data = Input::get('data');
        if (Input::get('test') == 'redirected') die('redirected');
        //Find Easter sundays
        for($i=0;$i<6;$i++)
        {
            $year[$i] = $thisyear + $i;
            $easter[$i] = date('d-m-Y',easter_date($year[$i]));
        }

        return view('house/createperiods', ['houses' => $houses,
            'houseid' => $houseid,
            'seasons' => $seasons,
            'periodlength' => $periodlength, 'seasons' => $seasons,
            'year' => $year,
            'easter' => $easter,
            'data' => $data,
            'lastperiods' => $lastperiods]);
    }

    /**
     * Inserts ranges of periods prepared from the previous function create the period. We chack if
     * the periods are already overlapped by leased periods and omit those.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function insertcreatedperiods()
    {
        if (!Gate::allows('Administrator')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        Input::merge(['test' => 'redirected']);
        $seasons = Input::get('seasons', 5);
        $periodlength = Input::get('periodlength', 7);
        $houseid = Input::get('houseid', 1);
        $ownerid = House::Find($houseid)->ownerid;
        $errors = [];
        $success = __('No new period have been created');
        $houses = House::filter(Input::all())->pluck('name', 'id')->toArray();
        $thisyear = Carbon::now()->year;



        //Set default data for each season
        $defaultperiod = [];
        $data = Input::get('data');

        $defaultperiod['basepersons'] = $data['basepersons'];
        $defaultperiod['maxpersons'] = $data['maxpersons'];
        $defaultperiod['baseprice'] = $data['baseprice'];
        $defaultperiod['personprice'] = $data['personprice'];
        $seasonstart = $data['seasonstart'];
        $seasonend = $data['seasonend'];


        for($i=0;$i<$seasons;$i++)
        {
            $seasonstart[$i] = Carbon::parse($seasonstart[$i]);
            $seasonend[$i] = Carbon::parse($seasonend[$i]);
            if ($seasonstart[$i]->gte($seasonend[$i])) $errors[$i] = __('Season').$i.': '.__('The end date must be after start date.');
            if ($i > 0)
            {
                if ($seasonend[$i-1]->gt($seasonstart[$i])) $errors[$i] = __('The start date must be after the previous end date.');
            }
        }

        //echo('Redirecting test: '.Input::get('test', 'nothing') . ' seasonend 0: ' .$seasonend[0]->format('d-m-Y'));
        if (sizeof($errors) > 0)
        {
            $success = __('Noting created, please check the errors below.');
        }

        //Find Easter sundays
        for($i=0;$i<6;$i++)
        {
            $year[$i] = $thisyear + $i;
            $easter[$i] = date('d-m-Y',easter_date($year[$i]));
        }

        //We create periods
        if (sizeof($errors) == 0)
        {
            for($i=0;$i<$seasons;$i++)
            {
                $from = clone($seasonstart[$i]);
                $to = clone($from);
                $to->addDays($periodlength);
                $counter = 0;
                //die("Periodlength: $periodlength from: ".$from." to: ".$to);
                while ($to->lte($seasonend[$i]))
                {
                    //check for committed periods
                    $count = Periodcontract::where('houseid', $houseid)->where('committed', 1)->whereDate('from', '<=', $from)->whereDate('to', '>=', $to)->count();
                    $count += Periodcontract::where('houseid', $houseid)->where('committed', 1)->whereDate('from', '<=', $to)->whereDate('to', '>=', $to)->count();
                    $count += Periodcontract::where('houseid', $houseid)->where('committed', 1)->whereDate('from', '<=', $from)->whereDate('to', '>=', $from)->count();

                    if($count > 0)
                    {
                        $errors[] = __('There are committed periods among the new, it will not be saved').'.';
                    }
                    else
                    {
                        //Check for overlapping non-committed periods and delete them
                        $periodfrom = Periodcontract::where('houseid', $houseid)->where('committed', null)->whereDate('from', '<=', $from)->whereDate('to', '>', $from)->orderBy('to', 'desc')->first();
                        $periodto = Periodcontract::where('houseid', $houseid)->where('committed', null)->whereDate('from', '<', $to)->whereDate('to', '>=', $to)->orderBy('to')->first();
                        if (($periodfrom) && ($periodto))
                        {
                            $overlappingtpdelete = Period::where('houseid', $houseid)->whereDate('to', '>=', $periodfrom->from)->whereDate('from', '<=', $periodto->to)->count();
                            if ($overlappingtpdelete > 0) $errors[] = __('At least one existing overlapping period has been deleted.');
                            Period::where('houseid', $houseid)->whereDate('to', '>=', $periodfrom->from)->whereDate('from', '<=', $periodto->to)->delete();
                        }
                        elseif($periodfrom)
                        {
                            Period::where('id', $periodfrom->id)->delete();
                            $errors[] = __('One existing overlapping period has been deleted.');
                        }

                        //There are no committed period clashing with the period we will create no, so we go ahead
                        //die(var_dump($defaultperiod));
                        //if (!array_key_exists($i, $defaultperiod)) die("Key $i does not exist in array:".var_dump($defaultperiod));
                        $period = new Period(['basepersons' => $defaultperiod['basepersons'][$i],
                                            'maxpersons' => $defaultperiod['maxpersons'][$i],
                                            'baseprice' => $defaultperiod['baseprice'][$i],
                                            'personprice' => $defaultperiod['personprice'][$i],
                                            'houseid' => $houseid,
                                            'ownerid' => $ownerid]);
                        $period->from = $from;
                        $period->to = $to;
                        $period->year = $from->year;
                        $period->weeknumber = $to->weekOfYear;
                        if(!$period->save())
                        {
                            die("Errors: ".var_dump($period->getErrors()));
                        }
                        $counter++;
                        $success = $counter.' '.__('new periods created');
                    }
                    $from = clone($to);
                    $to = $to->addDays($periodlength);
                }
            }
        }

        session()->flash('success', $success);
        return view('house/createperiods', [
            'houses' => $houses,
            'houseid' => $houseid,
            'seasons' => $seasons,
            'periodlength' => $periodlength, 'seasons' => $seasons,
            'year' => $year,
            'errors' => $errors,
            'easter' => $easter,
            'data' => $data]);

    }
}
