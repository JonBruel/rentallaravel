<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */

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

/**
 * Class ImportController, the only purpose is to
 * import all relevant data from the old system to the new
 * Laravel based rental system.
 * @package App\Http\Controllers
 */
class ImportController extends Controller
{

    /**
     * ImportController constructor.
     */
    public function __construct() {
        parent::__construct(\App\Models\Customer::class);
    }

    /**
     * Starts the command which imports all relevant data from the old rental system. This process
     * is run in the background as a daemon and in the view we keep track of the progress
     * by frequent ajax calls to a files which during the import is updated with the progress.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function importfromrental()
    {
        if (!Gate::allows('Supervisor')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));

        $process = new Process('php artisan command:importfromrental > /dev/null 2>&1 &', '/var/www/html/rentallaravel');
        $process->start();
        return view('import/importfromrental');
    }


}
