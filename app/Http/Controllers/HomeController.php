<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\PictureHelpers;
use Schema;
use Gate;
use ValidationAttributes;
use App\Models\HouseI18n;
use App;
use App\Models\Period;

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
            $info = HouseI18n::where('id', $this->houseId)->where('culture', 'LIKE', App::getLocale() . '%')->first()->$infotype;
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
        $yearstart = date('Y');
        //Below, we create the pseudo pager. This allows us to use tha standard
        //helper to navigate through the months or years
        //NOT implemented
        $starttime = $yearstart . '-' . date('m') . '-' . '01';

        $periodquery = Period::filter();

        return redirect('home/listhouses');

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
