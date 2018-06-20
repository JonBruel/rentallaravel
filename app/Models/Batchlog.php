<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Batchlog
 * 
 * @property int $id
 * @property int $statusid
 * @property int $posttypeid
 * @property int $batchtaskid
 * @property int $contractid
 * @property int $accountpostid
 * @property int $emailid
 * @property int $customerid
 * @property int $houseid
 * @property int $ownerid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Customer $customer
 * @property \App\Models\Posttype $posttype
 * @property \App\Models\Batchtask $batchtask
 * @property \App\Models\Contract $contract
 * @property \App\Models\Accountpost $accountpost
 * @property \App\Models\House $house
 * @property \App\Models\Batchstatus $batchstatus
 *
 * @package App\Models
 */
class Batchlog extends Eloquent
{
	protected $table = 'batchlog';

	protected $casts = [
		'statusid' => 'int',
		'posttypeid' => 'int',
		'batchtaskid' => 'int',
		'contractid' => 'int',
		'accountpostid' => 'int',
		'emailid' => 'int',
		'customerid' => 'int',
		'houseid' => 'int',
		'ownerid' => 'int'
	];

	protected $fillable = [
		'statusid',
		'posttypeid',
		'batchtaskid',
		'contractid',
		'accountpostid',
		'emailid',
		'customerid',
		'houseid',
		'ownerid'
	];

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'customerid');
	}

	public function posttype()
	{
		return $this->belongsTo(\App\Models\Posttype::class, 'posttypeid');
	}

	public function batchtask()
	{
		return $this->belongsTo(\App\Models\Batchtask::class, 'batchtaskid');
	}

	public function contract()
	{
		return $this->belongsTo(\App\Models\Contract::class, 'contractid');
	}

	public function accountpost()
	{
		return $this->belongsTo(\App\Models\Accountpost::class, 'accountpostid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function batchstatus()
	{
		return $this->belongsTo(\App\Models\Batchstatus::class, 'statusid');
	}
}
