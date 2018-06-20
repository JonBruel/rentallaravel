<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Customertype
 * 
 * @property int $id
 * @property string $customertype
 * 
 * @property \Illuminate\Database\Eloquent\Collection $customers
 * @property \Illuminate\Database\Eloquent\Collection $houses
 * @property \Illuminate\Database\Eloquent\Collection $rights
 *
 * @package App\Models
 */
class Customertype extends Eloquent
{
	protected $table = 'customertype';
	public $timestamps = false;

	protected $fillable = [
		'customertype'
	];

	public function customers()
	{
		return $this->hasMany(\App\Models\Customer::class, 'customertypeid');
	}

	public function houses()
	{
		return $this->hasMany(\App\Models\House::class, 'viewfilter');
	}

	public function rights()
	{
		return $this->hasMany(\App\Models\Right::class, 'customertypeid');
	}
}
