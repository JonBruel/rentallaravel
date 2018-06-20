<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Config
 * 
 * @property int $id
 * @property string $url
 * @property string $index
 * @property string $skin
 *
 * @package App\Models
 */
class Config extends Eloquent
{
	protected $table = 'config';
	public $timestamps = false;

	protected $fillable = [
		'url',
		'index',
		'skin'
	];
}
