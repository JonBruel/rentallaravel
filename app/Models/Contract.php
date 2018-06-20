<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Contract
 * 
 * @property int $id
 * @property int $houseid
 * @property int $ownerid
 * @property int $customerid
 * @property int $persons
 * @property string $theme
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $landingdatetime
 * @property \Carbon\Carbon $departuredatetime
 * @property string $message
 * @property float $duration
 * @property float $price
 * @property float $discount
 * @property float $finalprice
 * @property int $currencyid
 * @property int $categoryid
 * @property string $status
 * 
 * @property \App\Models\Category $category
 * @property \App\Models\Customer $customer
 * @property \App\Models\Currency $currency
 * @property \App\Models\House $house
 * @property \Illuminate\Database\Eloquent\Collection $accountposts
 * @property \Illuminate\Database\Eloquent\Collection $batchlogs
 * @property \Illuminate\Database\Eloquent\Collection $contractlines
 *
 * @package App\Models
 */
class Contract extends Eloquent
{
	protected $table = 'contract';

	protected $casts = [
		'houseid' => 'int',
		'ownerid' => 'int',
		'customerid' => 'int',
		'persons' => 'int',
		'duration' => 'float',
		'price' => 'float',
		'discount' => 'float',
		'finalprice' => 'float',
		'currencyid' => 'int',
		'categoryid' => 'int'
	];

	protected $dates = [
		'landingdatetime',
		'departuredatetime'
	];

	protected $fillable = [
		'houseid',
		'ownerid',
		'customerid',
		'persons',
		'theme',
		'landingdatetime',
		'departuredatetime',
		'message',
		'duration',
		'price',
		'discount',
		'finalprice',
		'currencyid',
		'categoryid',
		'status'
	];

	public function category()
	{
		return $this->belongsTo(\App\Models\Category::class, 'categoryid');
	}

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class, 'currencyid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function accountposts()
	{
		return $this->hasMany(\App\Models\Accountpost::class, 'contractid');
	}

	public function batchlogs()
	{
		return $this->hasMany(\App\Models\Batchlog::class, 'contractid');
	}

	public function contractlines()
	{
		return $this->hasMany(\App\Models\Contractline::class, 'contractid');
	}
}
