<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Auth;
use Schema;
use Gate;
use ValidationAttributes;
use Symfony\Component\Process\Process;

use App;
use DB;

use Illuminate\Support\Facades\Hash;

class ImportController extends Controller
{

    public function __construct() {
        parent::__construct(\App\Models\Customer::class);
    }

    public function importfromrental()
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));

        $process = new Process('php artisan command:importfromrental > /dev/null 2>&1 &', '/var/www/html/rentallaravel');
        $process->start();
        return view('import/importfromrental');
    }


}
