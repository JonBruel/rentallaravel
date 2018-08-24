<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;
use Carbon\Carbon;


/**
 * Class Currencyrate
 * 
 * @property int $id
 * @property int $currencyid
 * @property \Carbon\Carbon $created_at
 * @property float $rate
 * 
 * @property \App\Models\Currency $currency
 *
 * @package App\Models
 */
class Currencyrate extends BaseModel
{
	public $timestamps = false;

	protected $casts = [
		'currencyid' => 'int',
		'rate' => 'float'
	];

	protected $fillable = [
		'currencyid',
		'rate'
	];

	public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class, 'currencyid');
	}

    static function updateCurrencies()
    {
        $currencyidtable = array();
        foreach (Currency::all() as $currency) $currencyidtable[$currency->currencysymbol] = $currency->id;

        $url = file_get_contents('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
        $xml =  new \SimpleXMLElement($url) ;

        foreach($xml->Cube->Cube->Cube as $rate){
            if (array_key_exists((string)$rate["currency"], $currencyidtable))
            {
                $currencyrate = new Currencyrate();
                $currencyrate->currencyid = $currencyidtable[(string)$rate["currency"]];
                $currencyrate->rate = $rate["rate"];
                $currencyrate->created_at = Carbon::now()->tz('Europe/Copenhagen');
                $currencyrate->save();
            }
        }
    }

}
