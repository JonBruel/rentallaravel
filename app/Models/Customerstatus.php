<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Customerstatus
 * 
 * @property int $id
 * @property string $status
 * 
 * @property \Illuminate\Database\Eloquent\Collection $customers
 *
 * @package App\Models
 */
class Customerstatus extends Eloquent
{
	protected $table = 'customerstatus';
	public $timestamps = false;

	protected $fillable = [
		'status'
	];

	public function customers()
	{
		return $this->hasMany(\App\Models\Customer::class, 'status');
	}
}
