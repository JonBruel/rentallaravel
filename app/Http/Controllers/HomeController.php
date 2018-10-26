<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use App\Helpers\PictureHelpers;
use App\Helpers\ShowCalendar;
use App\Helpers\CreateValidationAttributes;
use Illuminate\Pagination\Paginator;
use Schema;
use Gate;
use ValidationAttributes;
use Illuminate\Support\Facades\Mail;
use App\Models\HouseI18n;
use App;
use Auth;
use App\Models\Periodcontract;
use App\Models\Testimonial;
use App\Models\House;
use App\Models\Customer;
use Carbon\Carbon;
use DB;
use App\Mail\DefaultMail;


/**
 * Class HomeController which is used for showing information about houses where the user is not required to be logged in.
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{

    public function __construct() {
        parent::__construct(\App\Models\House::class);

    }

    /**
     * Displays information about the house.
     *
     * @return \Illuminate\Http\Response
     */
    public function showinfo($infotype = 'description')
    {
        //if (!Auth::check()) return redirect('/login');
        $this->checkHouseChoice('home/showinfo/'.$infotype.'?menupoint='.session('menupoint', 10010));
        $defaultHouse = session('defaultHouse' , 1);
        if (Auth::viaRemember()) session()->flash('success', __('You are logged on.'));

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
            if (session($defaultHouse.'housedescriptions')) $info = session($defaultHouse.'housedescriptions')[App::getLocale()]->$infotype;
            else $info = HouseI18n::where('id', $defaultHouse)->where('culture', App::getLocale())->first()->$infotype;
            return view('home/showinfo', ['info' => $info, 'hidesalt' => true]);
        }
    }

    /**
     * The tokenLogin allows the user to login without password provided a remember_token is included in the query parameter.
     * The remember_token may expire and the user will be redirected to a login view.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function tokenlogin()
    {
        $this->checkToken();
        return redirect('/home/showinfo/description');
    }

    /**
     * Feeds the listtestimonials view, which if the user is logged in, allows for new testimonials. House owners will be given the option
     * of editing existing testimonials.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function listtestimonials()
    {
        $houseid = session('defaultHouse' , 1);

        //Following if used for testimoniallink
        $res = $this->checkToken();
        if($res) return redirect($res);

        $house = House::Find($houseid);
        $testimonials = Testimonial::where('houseid', $houseid)->sortable(['created_at' => 'desc'])->get();
        return view('home/listtestimonials', ['models' => $testimonials, 'administrator' => Gate::allows('Administrator'), 'house' => $house, 'houseid' => $houseid])->with('search', Input::all());
    }

    /**
     * This function is for the normal logged in customer to create a new testimonial.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createtestimonial()
    {
        if (!Auth::check()) return redirect('/login');
        $user = Auth::user();
        $testimonial = (new Testimonial(['houseid' => Input::get('houseid'),
            'userid' => $user->id,
            'text' => Input::get('text')]))->save();
        return redirect('home/listtestimonials')->with('success', __('Your testimonial is now included.'));
    }

    /**
     * The owner and above is allowed to delete testimonials.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroytestimonial($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        Testimonial::Find($id)->delete();
        return redirect('home/listtestimonials')->with('success', __('The testimonial is now deleted.'));
    }

    /**
     * The owner and above is allowed to edit and update testimonials.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edittestimonial($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $testimonial = Testimonial::Find($id);
        $fields = ['text', 'houseid', 'id', 'userid'];
        $vattr = (new CreateValidationAttributes($testimonial))->setCast('text', 'textarea')->setCast('id', 'hidden')->setCast('houseid', 'hidden')->setCast('userid', 'hidden');
        return view('home/edittestimonial', ['testimonial' => $testimonial, 'fields' => $fields, 'vattr' => $vattr]);
    }

    /**
     * The owner and above is allowed to edit and update testimonials.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatetestimonial($id)
    {
        if (!Gate::allows('Owner')) return redirect('/home')->with('warning', __('Somehow you the system tried to let you do something which is not allowed. So you are sent home!'));
        $testimonial = Testimonial::Find($id);
        $testimonial->text = Input::get('text');
        $testimonial->save();
        return redirect('home/listtestimonials')->with('success', __('The testimonial is now updated.'));
    }

    /**
     * The function feeds the view which shows the calender of the present and next two years.
     * The pager function has been used on a simple array to trick is to use the convenient paging functions in the view.
     * The view also shows the private usage of the house.
     *
     * @return mixed
     */
    public function checkbookings()
    {

        $this->checkHouseChoice('home/checkbookings'.'?menupoint='.session('menupoint'));

        $defaultHouse = session('defaultHouse' , config('app.default_house'));
        $house = House::findOrFail($defaultHouse);
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
            $calendar->link_to = '/contract/contractedit/0/';
            $calendar->culture = App::getLocale();
            $cal[$i] = $calendar->output_calendar();
            $starttime->addMonth();
        }

        return view('home/checkbookings', ['house' => $house, 'cal' => $cal, 'starttime' => $starttime, 'pager' => $pager, 'elements' => $elements, 'offset' => ($yearstart-1)])
                ->withHeader('Cache-Control', 'no-cache, must-revalidate');

    }

    /**
     * The menupoint leading to this function and view will not always be shown. If there only is one house, it will be hidden.
     * The view itself is much simpler than the case of the old rental system, and could be improved in the future.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        return view('home/search');
    }

    public function listhouses()
    {
        $defaultHouse = Input::get('defaultHouse',-1);
        $returnpath = Input::get('returnpath', 'home/showinfo/description?menupoint=10010');

        //When user has chosen we redirect to house
        if ($defaultHouse != -1)
        {
            session(['defaultHouse' => $defaultHouse]);
            return redirect(Input::get('returnpath', 'home/showinfo/description?menupoint=10010'));
        }

        $models = House::filter()->sortable()->paginate(10);
        return view('home/listhouses', ['models' => $models, 'returnpath' => $returnpath]);
    }

    /**
     * Feeds the information for the Google map for the house. The view itself has ajax calls back to
     * the AjaxController.
     *
     * @return mixed
     */
    public function showmap()
    {
        $googlekey = config('app.googlekey', 'AIzaSyCmXZ5CEhhFY3-qXoHRzs0XFK4a495LyxE');

        $this->checkHouseChoice('home/showmap'.'?menupoint='.session('menupoint'));

        $defaultHouse = session('defaultHouse' , -1);
        House::$ajax = true;
        $house = House::findOrFail($defaultHouse);
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
