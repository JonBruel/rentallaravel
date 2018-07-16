<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


/**
 * Class Contractline
 * 
 * @property int $periodid
 * @property int $contractid
 * @property float $quantity
 * @property int $id
 * 
 * @property \App\Models\Contract $contract
 * @property \App\Models\Period $period
 *
 * @package App\Models
 */
class Contractline extends BaseModel
{
	public $timestamps = false;

	protected $casts = [
		'periodid' => 'int',
		'contractid' => 'int',
		'quantity' => 'float'
	];

	protected $fillable = [
		'periodid',
		'contractid',
		'quantity'
	];

	public function contract()
	{
		return $this->belongsTo(\App\Models\Contract::class, 'contractid');
	}

	public function period()
	{
		return $this->belongsTo(\App\Models\Period::class, 'periodid');
	}
}
