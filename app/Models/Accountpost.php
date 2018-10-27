<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;
use Carbon\Carbon;


/**
 * Class Accountpost
 * 
 * @property int $id
 * @property int $houseid
 * @property int $ownerid
 * @property int $customerid
 * @property string $postsource
 * @property float $amount
 * @property int $currencyid
 * @property int $customercurrencyid
 * @property float $usedrate
 * @property string $text
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $contractid
 * @property int $posttypeid
 * @property int $postedbyid
 * @property int $passifiedby
 * @property \Carbon\Carbon $returndate
 * @property \App\Models\Customer $customer
 * @property \App\Models\Contract $contract
 * @property \App\Models\Posttype $posttype
 * @property \App\Models\Currency $currency
 * @property \App\Models\House $house
 * @property \Illuminate\Database\Eloquent\Collection $batchlogs
 *
 * @package App\Models
 */
class Accountpost extends BaseModel
{
	protected $casts = [
		'houseid' => 'int',
		'ownerid' => 'int',
		'customerid' => 'int',
		'amount' => 'float',
		'currencyid' => 'int',
		'customercurrencyid' => 'int',
		'usedrate' => 'float',
		'contractid' => 'int',
		'posttypeid' => 'int',
		'postedbyid' => 'int',
		'passifiedby' => 'int'
	];


	protected $fillable = [
		'houseid',
		'ownerid',
		'customerid',
		'postsource',
		'amount',
		'currencyid',
		'customercurrencyid',
		'usedrate',
		'text',
		'contractid',
		'posttypeid',
		'postedbyid',
		'passifiedby',
        'returndate'
	];

    public function modelFilter()
    {
        return $this->provideFilter(Filters\AccountpostFilter::class);
    }

    public function getAmountAttribute($value) {
        return $this->getNumberAttribute($value, 2);
    }

    public function setAmountAttribute($value) {
        $this->setNumberAttribute($value, 'amount');
    }

    protected $dates = ['returndate'];

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
            case 'posttypeid':
                return Posttype::all()->pluck('posttype', 'id')->map(function ($item, $key) {return $item = __($item);} )->toArray();
            case 'ownerid':
                return Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
            case 'customerid':
                return Customer::filter()->where('ownerid', $this->ownerid)->pluck('name', 'id')->toArray();
            case 'postedbyid':
                return Customer::where('ownerid', config('user.ownerid'))->pluck('name', 'id')->toArray();
            case 'houseid':
                return House::where('ownerid', $this->ownerid)->pluck('name', 'id')->toArray();
            case 'currencyid':
                return Currency::all()->pluck('currencysymbol', 'id')->toArray();
            case 'customercurrencyid':
                return Currency::all()->pluck('currencysymbol', 'id')->toArray();
            default:
                return null;
        }
    }

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function contract()
	{
		return $this->belongsTo(\App\Models\Contract::class, 'contractid');
	}

	public function posttype()
	{
		return $this->belongsTo(\App\Models\Posttype::class, 'posttypeid');
	}

	public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class, 'customercurrencyid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function batchlogs()
	{
		return $this->hasMany(\App\Models\Batchlog::class, 'accountpostid');
	}

	public static function activateAwaitingAccountposts($timeout)
    {
        $accountposts = Accountpost::where('posttypeid', 140)->whereDate('updated_at', '<', Carbon::now()->subSeconds($timeout))->get();
        foreach ($accountposts as $accountpost)
        {
            $accountpost->posttypeid = 110;
            $accountpost->text = 'Order changed';
            $accountpost->save();
        }
    }
}
