<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Culture
 * 
 * @property int $id
 * @property string $culture
 * @property string $culturename
 * @property int $currencyid
 * 
 * @property \App\Models\Currency $currency
 * @property \Illuminate\Database\Eloquent\Collection $customers
 *
 * @package App\Models
 */
class Culture extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'currencyid' => 'int'
	];

	protected $fillable = [
		'culture',
		'culturename',
		'currencyid'
	];

	public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class, 'currencyid');
	}

	public function customers()
	{
		return $this->hasMany(\App\Models\Customer::class, 'cultureid');
	}
}
