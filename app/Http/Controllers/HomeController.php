<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\PictureHelpers;
use App\Helpers\ShowCalendar;
use Illuminate\Pagination\Paginator;
use Schema;
use Gate;
use ValidationAttributes;
use App\Models\HouseI18n;
use App;
use App\Models\Period;
use \Datetime;
use \DateInterval;
use Carbon\Carbon;

class HomeController extends Controller
{
    //TODO: Let the user choose the house
    private $houseId = 1;

    public function __construct() {
        parent::__construct(\App\Models\House::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showinfo(Request $request, Response $response, $infotype = 'description')
    {
        if (session('defaultHouse' , -1) == -1)
        {
            $request>session()->flash('warning', 'Please find the house you want to check out!');
            $request>session()->flash('returnpath', 'home/showinfo/'.$infotype.'?menupoint='.session('menupoint', 10010));
            return redirect('home/listhouses');
        }

        if ($infotype == 'gallery')
        {
            $response->header('Cache-Control', 'no-cache, must-revalidate');
            $picturearray = PictureHelpers::getPictureArray($this->houseId, 1, 0);

            $galleryid = session('galleryid', 1);
            $fileinfo = PictureHelpers::getrandompicture($this->houseId, $galleryid);
            session(['lastran' => $fileinfo['r']]);
            $text = $fileinfo['text'];
            $picture = '<img src="' . $fileinfo['filepath'] . '" alt="' . $fileinfo['text'] . '"/>';
            return view('home/gallery', ['picturearray' => $picturearray, 'picture' => $picture, 'text' => $text]);
        }
        else
        {
            $info = HouseI18n::where('id', $this->houseId)->where('culture', App::getLocale())->first()->$infotype;
            return view('home/showinfo', ['info' => $info]);
        }
    }

    /*
     * Route::get('/home/listtestimonials', 'HomeController@listtestimonials');
     * Route::get('/home/showmap', 'HomeController@showmap');
     * Route::get('/home/checkbookings', 'HomeController@checkbookings');
     * Route::get('/home/listhouses', 'HomeController@listhouses');
     */

    public function checkbookings(Request $request, Response $response)
    {
        $response->header('Cache-Control', 'no-cache, must-revalidate');
        $defaultHouse = session('defaultHouse' , -1);
        if ($defaultHouse == -1)
        {
            $request>session()->flash('warning', 'Please find the house you want to check out!');
            $request>session()->flash('returnpath', 'home/showbookings'.'?menupoint='.session('menupoint'));
            return redirect('home/listhouses');
        }
        $house = $this->model::findOrFail($defaultHouse);

        $months = 12;
        $yearstart = Carbon::now()->year;
        $thismonthstart = Carbon::parse('first day of this month');
        //Below, we create the pager without a model. This allows us to use tha standard
        //helper to navigate through the months or years
        $page = $request->get('page', 1);
        $pager = new Paginator([1,1,1,1],1, (int)$page);
        $path = '/home/checkbookings?menupoint=10020';
        $pager->setPath($path);
        if ($page > 3) $pager->hasMorePagesWhen(false);
        $elements[0] = [$yearstart => $path.'&page=1', $yearstart+1 => $path.'&page=2', $yearstart+2 => $path.'&page=3', $yearstart+3 => $path.'&page=4'];

        $starttime = $thismonthstart->addYears($page-1);

        $periodquery = Period::filter();

        ShowCalendar::setVdays($defaultHouse, $periodquery, App::getLocale(), $starttime, $months);
        $cal = [];

        for ($i = 0; $i < 12; $i++) {
            $calendar = new ShowCalendar($starttime);
            $calendar->houseid = $defaultHouse;
            //$calendar->link_to = $this->url . '/contract/choseweeks/houseid/' . $this->houseid . '/cursor/0/restricttohouse/1/periodid/';
            $calendar->link_to = '/contract/choseweeks/houseid/' . $defaultHouse . '/cursor/0/restricttohouse/1/periodid/';
            $calendar->culture = App::getLocale();
            $cal[$i] = $calendar->output_calendar();
            $date = new DateTime($starttime);
            $date->add(new DateInterval('P1M'));
            $starttime = $date->format("Y-m-d");
            //if ($i == 1) die($starttime);
        }

        return view('home/checkbookings', ['house' => $house, 'cal' => $cal, 'starttime' => $starttime, 'pager' => $pager, 'elements' => $elements, 'offset' => ($yearstart-1)]);

    }

    public function listhouses(Request $request)
    {
        $defaultHouse = $request->query('defaultHouse',-1);

        //When user has chosen we redirect to house
        if ($defaultHouse != -1)
        {
            session(['defaultHouse' => $defaultHouse]);
            return redirect(session('returnpath', 'home/showinfo/description?menupoint=10010'));
        }

        $models = $this->model::sortable()->paginate(10);
        return view('home/listhouses', ['models' => $models]);
    }

}
