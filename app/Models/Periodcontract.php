<?php

/* hand-created from the view
 */

namespace App\Models;
use Carbon\Carbon;


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
     * Return an array with to elements: currencysymbol and rate
     */
    function getRate($culture)
    {
        $return = [];
        $culture = Culture::where('culture', $culture)->first();
        //TODO: if (!$culture) throw new Exception('No culture ' .  $culture . ' found, table entry may be deleted.');
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
        $r = __('From') . ' ' .Carbon::parse($this->from)->formatLocalized('%a %d %b %Y').' '.__('to').' '.Carbon::parse($this->to)->formatLocalized('%a %d %b %Y');
        return $r;
    }
}
