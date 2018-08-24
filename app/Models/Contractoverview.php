<?php

/* hand-created from the view
 */

namespace App\Models;


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
 * @property float $finalprice
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
		'persons' => 'int',
		'duration' => 'float',
		'finalprice' => 'float',
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
}
