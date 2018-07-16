<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

/**
 * Class Posttype
 * 
 * @property int $id
 * @property string $posttype
 * @property string $comment
 * @property int $defaultamount
 * 
 * @property \Illuminate\Database\Eloquent\Collection $accountposts
 * @property \Illuminate\Database\Eloquent\Collection $batchlogs
 * @property \Illuminate\Database\Eloquent\Collection $batchtasks
 *
 * @package App\Models
 */
class Posttype extends BaseModel
{
	public $timestamps = false;

	protected $casts = [
		'defaultamount' => 'int'
	];

	protected $fillable = [
		'posttype',
		'comment',
		'defaultamount'
	];

	public function accountposts()
	{
		return $this->hasMany(\App\Models\Accountpost::class, 'posttypeid');
	}

	public function batchlogs()
	{
		return $this->hasMany(\App\Models\Batchlog::class, 'posttypeid');
	}

	public function batchtasks()
	{
		return $this->hasMany(\App\Models\Batchtask::class, 'dontfireifposttypeid');
	}
}
