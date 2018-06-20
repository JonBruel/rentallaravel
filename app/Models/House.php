<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class House
 * 
 * @property int $id
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $country
 * @property string $www
 * @property float $latitude
 * @property float $longitude
 * @property bool $lockbatch
 * @property int $currencyid
 * @property int $ownerid
 * @property string $maidid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $viewfilter
 * @property float $prepayment
 * @property int $disttobeach
 * @property int $maxpersons
 * @property bool $isprivate
 * @property bool $dishwasher
 * @property bool $washingmachine
 * @property bool $spa
 * @property bool $pool
 * @property bool $sauna
 * @property bool $fireplace
 * @property bool $internet
 * @property bool $pets
 * 
 * @property \App\Models\Currency $currency
 * @property \App\Models\Customer $customer
 * @property \App\Models\Customertype $customertype
 * @property \Illuminate\Database\Eloquent\Collection $accountposts
 * @property \Illuminate\Database\Eloquent\Collection $batchlogs
 * @property \Illuminate\Database\Eloquent\Collection $batchtasks
 * @property \Illuminate\Database\Eloquent\Collection $contracts
 * @property \Illuminate\Database\Eloquent\Collection $emaillogs
 * @property \Illuminate\Database\Eloquent\Collection $house_i18ns
 * @property \Illuminate\Database\Eloquent\Collection $periods
 * @property \Illuminate\Database\Eloquent\Collection $standardemails
 * @property \Illuminate\Database\Eloquent\Collection $testimonials
 *
 * @package App\Models
 */
class House extends Eloquent
{
	protected $table = 'house';

	protected $casts = [
		'latitude' => 'float',
		'longitude' => 'float',
		'lockbatch' => 'bool',
		'currencyid' => 'int',
		'ownerid' => 'int',
		'viewfilter' => 'int',
		'prepayment' => 'float',
		'disttobeach' => 'int',
		'maxpersons' => 'int',
		'isprivate' => 'bool',
		'dishwasher' => 'bool',
		'washingmachine' => 'bool',
		'spa' => 'bool',
		'pool' => 'bool',
		'sauna' => 'bool',
		'fireplace' => 'bool',
		'internet' => 'bool',
		'pets' => 'bool'
	];

	protected $fillable = [
		'name',
		'address1',
		'address2',
		'address3',
		'country',
		'www',
		'latitude',
		'longitude',
		'lockbatch',
		'currencyid',
		'ownerid',
		'maidid',
		'viewfilter',
		'prepayment',
		'disttobeach',
		'maxpersons',
		'isprivate',
		'dishwasher',
		'washingmachine',
		'spa',
		'pool',
		'sauna',
		'fireplace',
		'internet',
		'pets'
	];

	public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class, 'currencyid');
	}

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function customertype()
	{
		return $this->belongsTo(\App\Models\Customertype::class, 'viewfilter');
	}

	public function accountposts()
	{
		return $this->hasMany(\App\Models\Accountpost::class, 'houseid');
	}

	public function batchlogs()
	{
		return $this->hasMany(\App\Models\Batchlog::class, 'houseid');
	}

	public function batchtasks()
	{
		return $this->hasMany(\App\Models\Batchtask::class, 'houseid');
	}

	public function contracts()
	{
		return $this->hasMany(\App\Models\Contract::class, 'houseid');
	}

	public function emaillogs()
	{
		return $this->hasMany(\App\Models\Emaillog::class, 'houseid');
	}

	public function house_i18ns()
	{
		return $this->hasMany(\App\Models\HouseI18n::class, 'id');
	}

	public function periods()
	{
		return $this->hasMany(\App\Models\Period::class, 'houseid');
	}

	public function standardemails()
	{
		return $this->hasMany(\App\Models\Standardemail::class, 'houseid');
	}

	public function testimonials()
	{
		return $this->hasMany(\App\Models\Testimonial::class, 'houseid');
	}
}
