<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


/**
 * Class Period
 * 
 * @property int $id
 * @property int $year
 * @property int $weeknumber
 * @property string $enddays
 * @property \Carbon\Carbon $from
 * @property \Carbon\Carbon $to
 * @property string $theme
 * @property int $houseid
 * @property int $ownerid
 * @property float $baseprice
 * @property int $basepersons
 * @property int $maxpersons
 * @property float $personprice
 * @property string $extra1
 * @property string $extra2
 * 
 * @property \App\Models\House $house
 * @property \App\Models\Customer $customer
 * @property \Illuminate\Database\Eloquent\Collection $contractlines
 *
 * @package App\Models
 */
class Period extends BaseModel
{
	protected $table = 'period';
	public $timestamps = false;

    public function modelFilter()
    {
        return $this->provideFilter(Filters\PeriodFilter::class);
    }

	protected $casts = [
		'year' => 'int',
		'weeknumber' => 'int',
		'houseid' => 'int',
		'ownerid' => 'int',
		'baseprice' => 'float',
		'basepersons' => 'int',
		'maxpersons' => 'int',
		'personprice' => 'float'
	];

	protected $dates = [
		'from',
		'to'
	];

	protected $fillable = [
		'year',
		'weeknumber',
		'enddays',
		'from',
		'to',
		'theme',
		'houseid',
		'ownerid',
		'baseprice',
		'basepersons',
		'maxpersons',
		'personprice',
		'extra1',
		'extra2'
	];

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function contractlines()
	{
		return $this->hasMany(\App\Models\Contractline::class, 'periodid');
	}


    static $committeds = [];
    static $contractids = [];
    //The function below returns an array which are used for the
    //calculation of the committed status of the contracts and the corresponding contractid.
    static function setPeriodStatus($houseid, $status)
    {
        //The execution below takes 5 ms
        for ($i = 1; $i <2; $i++)
        {
            //Get all committed contracts for the chosen houseid and status
            $contracts = Contract::where('houseid', $houseid)->where('status', $status)->get();

            foreach ($contracts as $contract)
            {
                $contractlines = $contract->contractlines;
                foreach ($contractlines as $contractline)
                {
                    $periodid = $contractline->periodid;
                    static::$committeds[$periodid] = true;
                    static::$contractids[$periodid] = $contract->id;
                }
            }
        }
    }

    function getContractid($status)
    {
        if (sizeof(static::$contractids) == 0) static::setPeriodStatus($this->houseid, $status);
        if (array_key_exists($this->id, static::$contractids)) return static::$contractids[$this->id];
        return null;
    }

    /*
     * Return an array with to elements: currencysymbol and rate
     */
    function getRate($culture)
    {
        $return = [];
        $culture = Culture::where('culture', $culture)->first();
        //if (!$culture) throw new Exception('No culture ' .  $culture . ' found, table entry may be deleted.');
        $customercurrencyid = $culture->currencyid;
        $customercurrency = Currency::find($customercurrencyid);
        $r['currencysymbol'] = $customercurrency->currencysymbol;

        $customercurrencyrate = $customercurrency->getRate();

        $housecurrencyid = House::find($this->houseid)->currencyid;
        $housecurrency = Currency::find($housecurrencyid);

        $housecurrencysymbol = $housecurrency->currencysymbol;
        $housecurrencyrate = $housecurrency->getRate();

        $r['rate'] = $customercurrencyrate/$housecurrencyrate;
        return $r;
    }

}
