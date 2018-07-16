<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


/**
 * Class Right
 * 
 * @property int $id
 * @property string $script
 * @property string $path
 * @property int $customertypeid
 * @property string $rights
 * 
 * @property \App\Models\Customertype $customertype
 *
 * @package App\Models
 */
class Right extends BaseModel
{
	public $timestamps = false;

	protected $casts = [
		'customertypeid' => 'int'
	];

	protected $fillable = [
		'script',
		'path',
		'customertypeid',
		'rights'
	];

	public function customertype()
	{
		return $this->belongsTo(\App\Models\Customertype::class, 'customertypeid');
	}
}
