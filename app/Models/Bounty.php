<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Bounty
 * 
 * @property int $id
 * @property string $version
 * @property int $userid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $text
 * @property string $extra
 * 
 * @property \App\Models\Customer $customer
 * @property \Illuminate\Database\Eloquent\Collection $bountyanswers
 *
 * @package App\Models
 */
class Bounty extends Eloquent
{
	protected $casts = [
		'userid' => 'int'
	];

	protected $fillable = [
		'version',
		'userid',
		'text',
		'extra'
	];

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'userid');
	}

	public function bountyanswers()
	{
		return $this->hasMany(\App\Models\Bountyanswer::class, 'bountyid');
	}
}
