<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Gate;
use Schema;
use ValidationAttributes;
use App\Models\House;
use App\Models\Customer;
use Auth;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $model;

    public function __construct(string $model) {
        $this->model = $model;
    }

    public function checkHouseChoice(string $returnpath = null)
    {
        if (session('defaultHouse') !== null) return false;
        if (House::filter()->count() == 1)
        {
            session(['defaultHouse' => House::filter()->first()->id]);
            return false;
        }
        else
        {
            session()->flash('warning', 'Please find the house you want to check out!');
            return redirect('home/listhouses?returnpath='.$returnpath);
        }
    }

    protected function doSaveAndRetrieve(string $parameter, $default = null)
    {
        $testvalue = Input::get($parameter);
        if ($testvalue != null)
        {
            session([$parameter => $testvalue]);
            return $testvalue;
        }
        return session($parameter, $default);
    }

    //Does not work
    protected function setRights($usertype, $message = null)
    {
        if (!$message) $message = 'Somehow you the system tried to let you do something which is not allowed. So you are sent home!';
        if (!Gate::allows($usertype)) return redirect('/home')->with('warning', __($message));
        return null;
    }

    protected function generaledit($id, $modelclass, $view, $onlyFields = null, $plusFields = null, $minusFields = null, $casts = null)
    {
        //Find page from id
        if (Input::get('page') == null) {
            $models = $modelclass::filter(Input::all())->sortable('id')->pluck('id')->all();
            $page = array_flip($models)[$id]+1;
            Input::merge(['page' => $page]);
        }

        $models = $modelclass::filter(Input::all())->sortable('id')->paginate(1);

        if ($onlyFields) $fields = $onlyFields;
        else $fields = Schema::getColumnListing($models[0]->getTable());
        if ($minusFields) $fields = array_diff($fields, $minusFields);
        if ($plusFields) $fields = $fields + $plusFields;

        $vattr = new ValidationAttributes($models[0]);
        if ($casts) $vattr->setCasts($casts);

        return view($view, ['models' => $models, 'fields' => $fields, 'vattr' => $vattr]);
    }

    protected function generalupdate($id, $modelclass, $okMessage, $redirectOk, $redirectError = null, $onlyFields = null, $plusFields = null, $minusFields = null)
    {
        $model =  $modelclass::findOrFail($id);

        if ($onlyFields) $fields = $onlyFields;
        else $fields = Schema::getColumnListing($model->getTable());
        if ($minusFields) $fields = array_diff($fields, $minusFields);
        if ($plusFields) $fields = $fields + $plusFields;

        if (!$redirectError) $redirectError = $redirectOk;

        foreach ($fields as $field){
            $model->$field = Input::get($field);
        }
        //We save. The save validates after the Mutators have been used.
        $errors = '';
        $success = __($okMessage).'!';
        if (!$model->save()) {
            $errors = $model->getErrors();
            $success = '';
        }
        if ($errors != '') return redirect($redirectError)->with('success', $success)->with('errors',$errors)->withInput();
        return redirect($redirectOk)->with('success', $success)->withInput();
    }

    /**
     * Check if user is logged in. If not we check the remember_token and login based on that.
     * The function is used for the links sent to the customers, allowing them to get directly
     * to the itenary or testimonial page without logging in. It may not always succees as the
     * remember_token might have been changed. This happens if the customer logs out before the
     * link is used.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function checkToken()
    {
        //Following if used for testimoniallink
        if (!Auth::check())
        {
            if (Input::get('houseid', -1) != -1) {
                $houseid = Input::get('houseid');
                session(['houseid' > $houseid]);
            }

            //Check of token
            if (Input::get('remember_token')) {
                $customer = Customer::where('remember_token', Input::get('remember_token'))->first();
                if ($customer)
                {
                    Auth::loginUsingId($customer->id);
                    return Input::get('redirectTo');
                }
                else {
                    session(['redirectTo' => Input::get('redirectTo')]);
                    return redirect('\login')->withInput();
                }
            }
        }
    }

}
