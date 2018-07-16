<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

/**
 * Class StandardemailI18n
 * 
 * @property int $id
 * @property string $culture
 * @property string $contents
 * 
 * @property \App\Models\Standardemail $standardemail
 *
 * @package App\Models
 */
class StandardemailI18n extends BaseModel
{
	protected $table = 'standardemail_i18n';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'contents'
	];

	public function standardemail()
	{
		return $this->belongsTo(\App\Models\Standardemail::class, 'id');
	}
}
