<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Batchfunction
 * 
 * @property int $id
 * @property string $batchfunction
 * 
 * @property \Illuminate\Database\Eloquent\Collection $batchtasks
 *
 * @package App\Models
 */
class Batchfunction extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'batchfunction'
	];

	public function batchtasks()
	{
		return $this->hasMany(\App\Models\Batchtask::class, 'batchfunctionid');
	}
}
