<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


/**
 * Class Batchtask
 * 
 * @property int $id
 * @property string $name
 * @property int $posttypeid
 * @property int $emailid
 * @property int $batchfunctionid
 * @property string $mailto
 * @property float $paymentbelow
 * @property bool $usepaymentbelow
 * @property int $requiredposttypeid
 * @property bool $userequiredposttypeid
 * @property int $timedelaystart
 * @property bool $usetimedelaystart
 * @property int $timedelayfrom
 * @property bool $usetimedelayfrom
 * @property int $addposttypeid
 * @property bool $useaddposttypeid
 * @property int $dontfireifposttypeid
 * @property bool $usedontfireifposttypeid
 * @property int $ownerid
 * @property int $houseid
 * @property \Carbon\Carbon $activefrom
 * @property int $active
 * 
 * @property \App\Models\Posttype $posttype
 * @property \App\Models\Standardemail $standardemail
 * @property \App\Models\Customer $customer
 * @property \App\Models\House $house
 * @property \App\Models\Batchfunction $batchfunction
 * @property \Illuminate\Database\Eloquent\Collection $batchlogs
 *
 * @package App\Models
 */
class Batchtask extends BaseModel
{
	protected $table = 'batchtask';
	public $timestamps = false;

    public function modelFilter()
    {
        return $this->provideFilter(Filters\BatchtaskFilter::class);
    }

	protected $casts = [
		'posttypeid' => 'int',
		'emailid' => 'int',
		'batchfunctionid' => 'int',
		'paymentbelow' => 'float',
		'usepaymentbelow' => 'bool',
		'requiredposttypeid' => 'int',
		'userequiredposttypeid' => 'bool',
		'timedelaystart' => 'int',
		'usetimedelaystart' => 'bool',
		'timedelayfrom' => 'int',
		'usetimedelayfrom' => 'bool',
		'addposttypeid' => 'int',
		'useaddposttypeid' => 'bool',
		'dontfireifposttypeid' => 'int',
		'usedontfireifposttypeid' => 'bool',
		'ownerid' => 'int',
		'houseid' => 'int',
		'active' => 'int'
	];

	protected $dates = [
		'activefrom'
	];

	protected $fillable = [
		'name',
		'posttypeid',
		'emailid',
		'batchfunctionid',
		'mailto',
		'paymentbelow',
		'usepaymentbelow',
		'requiredposttypeid',
		'userequiredposttypeid',
		'timedelaystart',
		'usetimedelaystart',
		'timedelayfrom',
		'usetimedelayfrom',
		'addposttypeid',
		'useaddposttypeid',
		'dontfireifposttypeid',
		'usedontfireifposttypeid',
		'ownerid',
		'houseid',
		'activefrom',
		'active'
	];


	public $rules = [
        'name' => ['required', 'between:3,51'],
        'paymentbelow' => ['required', 'between:-0.991,0.991', 'numeric'],
    ];


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
            case 'posttypeid':
                return  ['' => __('Choose accounttype')] + Posttype::all()->pluck('posttype', 'id')
                        ->map(function ($item, $key) {return $item = __($item);} )
                        ->toArray();
            case 'emailid':
                return Standardemail::where('houseid', $this->houseid)->pluck('description', 'id')
                        ->map(function ($item, $key) {return $item = __($item);} )
                        ->toArray();
            case 'batchfunctionid':
                return  Batchfunction::all()->pluck('batchfunction', 'id')
                        ->map(function ($item, $key) {return $item = __($item);} )
                        ->toArray();
            case 'mailto':
                return  Customertype::all()->pluck('customertype', 'id')
                        ->map(function ($item, $key) {return $item = __($item);} )
                        ->toArray();
            case 'requiredposttypeid':
                return ['' => __('Choose accounttype')] + Posttype::all()->pluck('posttype', 'id')
                        ->map(function ($item, $key) {return $item = __($item);} )
                        ->toArray();
            case 'dontfireifposttypeid':
                return  ['' => __('Choose accounttype')] + Posttype::all()->pluck('posttype', 'id')
                        ->map(function ($item, $key) {return $item = __($item);} )
                        ->toArray();
            case 'addposttypeid':
                return  ['' => __('Choose accounttype')] + Posttype::all()->pluck('posttype', 'id')
                        ->map(function ($item, $key) {return $item = __($item);} )
                        ->toArray();
            case 'ownerid':
                return  Customer::where('customertype', 10)->pluck('name', 'id')->toArray();
            case 'houseid':
                return  House::where('ownerid', $this->ownerid)->pluck('name', 'id')->toArray();
            default:
                return null;
        }
    }

    /* Fix this, when we update the value is set to 0
    public function getPaymentbelowAttribute($value) {
        return $this->getNumberAttribute($value, 0, '', ['style' => 'percent']);
    }

    public function setPaymentbelowAttribute($value) {
        $this->setNumberAttribute($value, 'paymentbelow');
    */

	public function posttype()
	{
		return $this->belongsTo(\App\Models\Posttype::class, 'dontfireifposttypeid');
	}

	public function standardemail()
	{
		return $this->belongsTo(\App\Models\Standardemail::class, 'emailid');
	}

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function batchfunction()
	{
		return $this->belongsTo(\App\Models\Batchfunction::class, 'batchfunctionid');
	}

	public function batchlogs()
	{
		return $this->hasMany(\App\Models\Batchlog::class, 'batchtaskid');
	}
}
