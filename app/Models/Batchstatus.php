<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;



/**
 * Class Batchstatus
 * 
 * @property int $id
 * @property string $status
 * 
 * @property \Illuminate\Database\Eloquent\Collection $batchlogs
 *
 * @package App\Models
 */
class Batchstatus extends BaseModel
{
	protected $table = 'batchstatus';
	public $timestamps = false;

	protected $fillable = [
		'status'
	];

	public function batchlogs()
	{
		return $this->hasMany(\App\Models\Batchlog::class, 'statusid');
	}
}
