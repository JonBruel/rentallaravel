<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 2019-01-25
 * Time: 17:16
 */

namespace App\Helpers;

use DB;
use Illuminate\Support\Facades\Log;

class ConfigFromDB
{

    public static function configFromDB($webaddress = 'rentallaravel.consiglia.dk')
    {
        $config = DB::table('config')->where('url', $webaddress)->first();
        if ($config) {
            $json = $config->index;
            $codearray = json_decode($json, true);
            if (json_last_error() == JSON_ERROR_NONE) foreach ($codearray as $key => $value) config([$key => $value]);
            else {
                Log::notice('The joson: '.$json.' could not be parsed, json error: '.json_last_error());
                session()->flash('warning', 'There is an error in the setting json for the url: '.$webaddress);
            }
        }
        else {
            session()->flash('warning', 'There is no settings for mailt and other url-dependent parameters for the url: '.$webaddress);
        }
    }

}
