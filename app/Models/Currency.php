<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


/**
 * Class Currency
 * 
 * @property int $id
 * @property string $currencyname
 * @property string $currencysymbol
 * @property float $rate
 * @property bool $listed
 * @property int $code
 * 
 * @property \Illuminate\Database\Eloquent\Collection $accountposts
 * @property \Illuminate\Database\Eloquent\Collection $contracts
 * @property \Illuminate\Database\Eloquent\Collection $cultures
 * @property \Illuminate\Database\Eloquent\Collection $currencyrates
 * @property \Illuminate\Database\Eloquent\Collection $houses
 *
 * @package App\Models
 */
class Currency extends BaseModel
{
	public $timestamps = false;

	protected $casts = [
		'rate' => 'float',
		'listed' => 'bool',
		'code' => 'int'
	];

	protected $fillable = [
		'currencyname',
		'currencysymbol',
		'rate',
		'listed',
		'code'
	];

	public function accountposts()
	{
		return $this->hasMany(\App\Models\Accountpost::class, 'customercurrencyid');
	}

	public function contracts()
	{
		return $this->hasMany(\App\Models\Contract::class, 'currencyid');
	}

	public function cultures()
	{
		return $this->hasMany(\App\Models\Culture::class, 'currencyid');
	}

	public function currencyrates()
	{
		return $this->hasMany(\App\Models\Currencyrate::class, 'currencyid');
	}

	public function houses()
	{
		return $this->hasMany(\App\Models\House::class, 'currencyid');
	}
}
