<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Standardemail
 * 
 * @property int $id
 * @property string $description
 * @property int $ownerid
 * @property int $houseid
 * @property string $extra
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Customer $customer
 * @property \App\Models\House $house
 * @property \Illuminate\Database\Eloquent\Collection $batchtasks
 * @property \Illuminate\Database\Eloquent\Collection $standardemail_i18ns
 *
 * @package App\Models
 */
class Standardemail extends Eloquent
{
	protected $table = 'standardemail';

	protected $casts = [
		'ownerid' => 'int',
		'houseid' => 'int'
	];

	protected $fillable = [
		'description',
		'ownerid',
		'houseid',
		'extra'
	];

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function batchtasks()
	{
		return $this->hasMany(\App\Models\Batchtask::class, 'emailid');
	}

	public function standardemail_i18ns()
	{
		return $this->hasMany(\App\Models\StandardemailI18n::class, 'id');
	}
}
