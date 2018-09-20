<?php

/* hand-created from the view
 */

namespace App\Models;
use Carbon\Carbon;
use App;


/**
 * Class Periodcontract
 *
 * @property float $prepaid
 * @property int $id
 * @property int $houseid
 * @property int $ownerid
 * @property \Carbon\Carbon $from
 * @property \Carbon\Carbon $to
 * @property int $weeknumber
 * @property int $contractid
 * @property float $committed
 * @property int $uncommitted
 * @property int $customerid
 * @property float $personprice
 * @property int $maxpersons
 * @property int $basepersons
 * @property float $baseprice
 *
 *
 * @package App\Models
 *
 * Periodcontract is a view
 *
 */
class Periodcontract extends BaseModel
{
	protected $table = 'periodcontract';
	public $timestamps = false;


    public function modelFilter()
    {
        return $this->provideFilter(Filters\PeriodcontractFilter::class);
    }


	protected $casts = [
		'prepaid' => 'float',
		'id' => 'int',
		'houseid' => 'int',
		'ownerid' => 'int',
		'weeknumber' => 'int',
		'contractid' => 'int',
		'committed' => 'float',
		'uncommitted' => 'int',
        'customerid' => 'int',
        'personprice' => 'float',
        'maxpersons' => 'int',
        'basepersons' => 'int',
        'baseprice' => 'float'
	];

    protected $dates = [
        'from',
        'to'
    ];

	protected $fillable = [
	];

    /*
     * Return an array with to elements: currencysymbol and rate. The rate calculated
     * is the rate used to calculate the price in the customer currency from the price
     * of the period, which is based on the house currency.
     */
    function getRate($cultureid, $customercurrencyid = null)
    {
        $return = [];
        if ($customercurrencyid == null)
        {
            $culture = Culture::where('culture', $cultureid)->first();
            //TODO: if (!$culture) throw new Exception('No culture ' .  $culture . ' found, table entry may be deleted.');
            $customercurrencyid = $culture->currencyid;
        }

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

    function getContractid()
    {
        if (($this->committed > 0) or ($this->prepaid > 0)) {
            return $this->contractid;
        }
        return null;
    }

    /*
     *  A nice string showing the start and end days using the
     *  localized day and month names.
     */
    public function getEnddays($culture = null)
    {
        if ($culture)
        {
            setlocale(LC_TIME, $culture);
            App::setLocale($culture);
        }
        $r = __('From') . ' ' .Carbon::parse($this->from)->formatLocalized('%a %d %b %Y').' '.__('to').' '.Carbon::parse($this->to)->formatLocalized('%a %d %b %Y');
        return $r;
    }
}
