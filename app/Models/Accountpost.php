<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


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
 * 
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

	protected $dates = [
		'returndate'
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
}
