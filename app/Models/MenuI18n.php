<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MenuI18n
 * 
 * @property int $id
 * @property string $culture
 * @property string $text
 * 
 * @property \App\Models\Menu $menu
 *
 * @package App\Models
 */
class MenuI18n extends Eloquent
{
	protected $table = 'menu_i18n';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'text'
	];

	public function menu()
	{
		return $this->belongsTo(\App\Models\Menu::class, 'id');
	}
}
