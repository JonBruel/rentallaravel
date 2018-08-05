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
use App\Models\Contract;
use Carbon\Carbon;

class ContractController extends Controller
{
    //TODO: Let the user choose the house
    private $houseId = 1;

    public function __construct() {
        parent::__construct(\App\Models\Contract::class);
    }


}
