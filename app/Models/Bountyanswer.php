<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Bountyanswer
 * 
 * @property int $id
 * @property int $bountyid
 * @property int $userid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $text
 * @property string $version
 * 
 * @property \App\Models\Bounty $bounty
 * @property \App\Models\Customer $customer
 *
 * @package App\Models
 */
class Bountyanswer extends Eloquent
{
	protected $casts = [
		'bountyid' => 'int',
		'userid' => 'int'
	];

	protected $fillable = [
		'bountyid',
		'userid',
		'text',
		'version'
	];

	public function bounty()
	{
		return $this->belongsTo(\App\Models\Bounty::class, 'bountyid');
	}

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'userid');
	}
}
