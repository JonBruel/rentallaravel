<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


/**
 * Class Currencyrate
 * 
 * @property int $id
 * @property int $currencyid
 * @property \Carbon\Carbon $created_at
 * @property float $rate
 * 
 * @property \App\Models\Currency $currency
 *
 * @package App\Models
 */
class Currencyrate extends BaseModel
{
	public $timestamps = false;

	protected $casts = [
		'currencyid' => 'int',
		'rate' => 'float'
	];

	protected $fillable = [
		'currencyid',
		'rate'
	];

	public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class, 'currencyid');
	}
}
