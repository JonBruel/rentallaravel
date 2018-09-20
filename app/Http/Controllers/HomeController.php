<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use App\Helpers\PictureHelpers;
use App\Helpers\ShowCalendar;
use Illuminate\Pagination\Paginator;
use Schema;
use Gate;
use ValidationAttributes;
use Illuminate\Support\Facades\Mail;
use App\Models\HouseI18n;
use App;
use Auth;
use App\Models\Periodcontract;
use Carbon\Carbon;
use DB;
use App\Mail\DefaultMail;

class HomeController extends Controller
{
    public function __construct() {
        parent::__construct(\App\Models\House::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showinfo(Request $request, $infotype = 'description')
    {
        $this->checkHouseChoice($request, 'home/showinfo/'.$infotype.'?menupoint='.session('menupoint', 10010));
        $defaultHouse = session('defaultHouse' , 1);


        //Testing mail.
        //TODO: Remove it in production version.
        if (\Auth::check()) {
            $user = Auth::user();
            //public function __construct($contents, $fromaddress = 'jbr@consiglia.dk', $fromName = 'testFromName', $toName = '', $attachements = [])
            //Mail::to('jbr@consiglia.dk')->send(new DefaultMail('<br />Dette er en prøve.<br /><br />', 'jbr@consiglia.dk', 'Jon Brüel', 'Jon'));
        }

        if ($infotype == 'gallery')
        {
            $picturearray = PictureHelpers::getPictureArray($defaultHouse, 1, 0);
            $galleryid = session('galleryid', 1);
            $fileinfo = PictureHelpers::getrandompicture($defaultHouse, $galleryid);
            session(['lastran' => $fileinfo['r']]);
            $text = $fileinfo['text'];
            $picture = '<img src="' . $fileinfo['filepath'] . '" alt="' . $fileinfo['text'] . '"/>';
            return view('home/gallery', ['picturearray' => $picturearray, 'picture' => $picture, 'text' => $text])
                    ->withHeader('Cache-Control', 'no-cache, must-revalidate');
        }
        else
        {
            $info = HouseI18n::where('id', $defaultHouse)->where('culture', App::getLocale())->first()->$infotype;

            //Find first vacant week
            $freeperiod = Periodcontract::filter()->where('from', '>', Carbon::now())->whereNull('committed')->orderBy('from')->first();
            $firstFree = ($freeperiod)?Carbon::parse($freeperiod->from)->toDateString('Y-m-d') . ' ' . __('to') . ' ' . Carbon::parse($freeperiod->to)->toDateString('Y-m-d'):'';
            return view('home/showinfo', ['info' => $info, 'firstFree' => $firstFree]);
        }
    }

    /*
     * Route::get('/home/listtestimonials', 'HomeController@listtestimonials');
     * Route::get('/home/showmap', 'HomeController@showmap');
     * Route::get('/home/checkbookings', 'HomeController@checkbookings');
     * Route::get('/home/listhouses', 'HomeController@listhouses');
     */

    public function checkbookings()
    {

        $this->checkHouseChoice('home/checkbookings'.'?menupoint='.session('menupoint'));

        $defaultHouse = session('defaultHouse' , -1);

        $house = $this->model::findOrFail($defaultHouse);
        $months = 12;
        $yearstart = Carbon::now()->year;
        $thismonthstart = Carbon::parse('first day of this month');

        //Below, we create the pager without a model. This allows us to use tha standard
        //helper to navigate through the months or years
        $page = Input::get('page', 1);
        $pager = new Paginator([1,1,1,1],1, (int)$page);
        $path = '/home/checkbookings?menupoint=10020';
        $pager->setPath($path);
        if ($page > 3) $pager->hasMorePagesWhen(false);
        $elements[0] = [$yearstart => $path.'&page=1', $yearstart+1 => $path.'&page=2', $yearstart+2 => $path.'&page=3', $yearstart+3 => $path.'&page=4'];

        $starttime = $thismonthstart->addYears($page-1);

        $periodquery = Periodcontract::filter();

        ShowCalendar::setVdays($defaultHouse, $periodquery, App::getLocale(), $starttime, $months);
        $cal = [];

        for ($i = 0; $i < 12; $i++) {
            $calendar = new ShowCalendar($starttime);
            $calendar->houseid = $defaultHouse;
            //$calendar->link_to = $this->url . '/contract/choseweeks/houseid/' . $this->houseid . '/cursor/0/restricttohouse/1/periodid/';
            $calendar->link_to = '/contract/adminedit/0/';
            $calendar->culture = App::getLocale();
            $cal[$i] = $calendar->output_calendar();
            $starttime->addMonth();
        }

        return view('home/checkbookings', ['house' => $house, 'cal' => $cal, 'starttime' => $starttime, 'pager' => $pager, 'elements' => $elements, 'offset' => ($yearstart-1)])
                ->withHeader('Cache-Control', 'no-cache, must-revalidate');

    }

    public function listhouses()
    {
        $defaultHouse = Input::get('defaultHouse',-1);

        //When user has chosen we redirect to house
        if ($defaultHouse != -1)
        {
            session(['defaultHouse' => $defaultHouse]);
            return redirect(session('returnpath', 'home/showinfo/description?menupoint=10010'));
        }

        if (session('returnpath')) session()->keep(['returnpath']);

        $models = $this->model::filter()->sortable()->paginate(10);
        return view('home/listhouses', ['models' => $models]);
    }


    public function showmap()
    {
        $googlekey = config('app.googlekey', 'AIzaSyCmXZ5CEhhFY3-qXoHRzs0XFK4a495LyxE');

        $this->checkHouseChoice('home/showmap'.'?menupoint='.session('menupoint'));

        $defaultHouse = session('defaultHouse' , -1);
        $this->model::$ajax = true;
        $house = $this->model::findOrFail($defaultHouse);
        $housefields = $house->toArray();
        $veryShortDescription = HouseI18n::where('id', $defaultHouse)->where('culture', App::getLocale())->first()->veryshortdescription;
        $housefields['veryShortDescription'] = $veryShortDescription;

        //Google translation test
        //$gt = new GoogleTranslateWrapper();
        //$gt->selfTest();

        return view('home/showmap', ['house' => $house, 'veryShortDescription'  => $veryShortDescription, 'googlekey' => $googlekey, 'housefields' => json_encode($housefields)])
            ->withHeader('Cache-Control', 'no-cache, must-revalidate');
    }

}
