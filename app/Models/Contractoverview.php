<?php

/* hand-created from the view
 */

namespace App\Models;
use App;


/**
 * Class Contractoverview
 *
 * @property int $id
 * @property int $houseid
 * @property int $ownerid
 * @property int $customerid
 * @property int $persons
 * @property \Carbon\Carbon $created_at
 * @property float $duration
 * @property float $price
 * @property float $finalprice
 * @property float $discount
 * @property int $categoryid
 * @property float $contractamount
 * @property float $paid
 * @property \Carbon\Carbon $landingtime
 * @property \Carbon\Carbon $departuretime
 * @property string $status
 * @property int $currencyid
 * @property int $customercurrencyid
 * @property \Carbon\Carbon $from
 * @property \Carbon\Carbon $to
 *
 *
 * @package App\Models
 *
 * Contractoverview is a view
 *
 */
class Contractoverview extends BaseModel
{
	protected $table = 'contractoverview';
	public $timestamps = false;

    public $sortable = [
        'house.name',
        'customer.name',
        'id',
        'customerid',
    ];

    public function modelFilter()
    {
        return $this->provideFilter(Filters\ContractoverviewFilter::class);
    }

	protected $casts = [
		'id' => 'int',
		'houseid' => 'int',
		'ownerid' => 'int',
		'customerid' => 'int',
		'duration' => 'float',
        'price' => 'float',
		'finalprice' => 'float',
        'discount' => 'float',
        'categoryid' => 'int',
        'contractamount' => 'float',
        'paid' => 'float',
        'currencyid' => 'int',
        'customercurrencyid' => 'int'
	];

    protected $dates = [
        'created_at',
        'landingdatetime',
        'departuredatetime',
        'from',
        'to'
    ];

	protected $fillable = [
	];

    /*
     * This function is used to show the relevant associated
     * user-friendly value as opposed to showing the id.
     * Performance: as we are making up to 4 queries, it does take some time.
     * Measured to around 5 ms.
     */
    public function withBelongsTo($fieldname)
    {
        switch ($fieldname)
        {
            /**/
            case 'houseid':
                return $this->house->name;
            case 'ownerid':
                return $this->owner->name;
            case 'customerid':
                return $this->customer->name;
            case 'categoryid':
                return $this->category->name;
            case 'curencyid':
                return $this->currency->currencysymbol;
            case 'from':
                return $this->contract->getPeriodtext(App::getLocale());

            default:
                return $this->$fieldname;
        }
    }

    /*
     * Retuns an array of keys and values to be used in forms for select boxes. Typical uses
     * are filters, e.g selection housed owner by a specific owner.
     *
     * Retuns null if no select boxes are to be used.
     */
    public function withSelect($fieldname)
    {
        switch ($fieldname)
        {
            /**/
            case 'customertypeid':
                return  Customertype::all()->pluck('customertype', 'id')->toArray();
            case 'ownerid':
                return Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
            //case 'customerid':
            //    return Customer::filter()->where('customertypeid', 1000)->where('ownerid', $this->ownerid)->pluck('name', 'id')->toArray();
            case 'cultureid':
                return  Culture::all()->pluck('culturename', 'id')->toArray();

            default:
                return null;
        }
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customerid');
    }

    public function owner()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
    }

    public function house()
    {
        return $this->belongsTo(\App\Models\House::class, 'houseid');
    }

    public function contract()
    {
        return $this->belongsTo(\App\Models\Contract::class, 'id');
    }
}
