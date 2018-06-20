<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Emaillog
 * 
 * @property int $id
 * @property int $customerid
 * @property int $houseid
 * @property int $ownerid
 * @property string $from
 * @property string $to
 * @property string $cc
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $text
 * 
 * @property \App\Models\Customer $customer
 * @property \App\Models\House $house
 *
 * @package App\Models
 */
class Emaillog extends Eloquent
{
	protected $table = 'emaillog';

	protected $casts = [
		'customerid' => 'int',
		'houseid' => 'int',
		'ownerid' => 'int'
	];

	protected $fillable = [
		'customerid',
		'houseid',
		'ownerid',
		'from',
		'to',
		'cc',
		'text'
	];

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}
}
