<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


/**
 * Class Testimonial
 * 
 * @property int $id
 * @property int $houseid
 * @property int $userid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $text
 * @property string $extra
 * 
 * @property \App\Models\Customer $customer
 * @property \App\Models\House $house
 *
 * @package App\Models
 */
class Testimonial extends BaseModel
{
	protected $casts = [
		'houseid' => 'int',
		'userid' => 'int'
	];

	protected $fillable = [
		'houseid',
		'userid',
		'text',
		'extra'
	];

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'userid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}
}
