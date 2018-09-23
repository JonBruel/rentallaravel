<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Gate;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $model;

    public function __construct(string $model) {
        $this->model = $model;
    }

    public function checkHouseChoice(string $returnpath = null)
    {
        if (session('defaultHouse' , config('app.default_house')) != -1) return false;
        if ($this->model::filter()->count() == 1)
        {
            session(['defaultHouse' => $this->model::filter()->first()->id]);
            return false;
        }
        else
        {
            session()->flash('warning', 'Please find the house you want to check out!');
            return redirect('home/listhouses')->with('returnpath', $returnpath);
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
}
