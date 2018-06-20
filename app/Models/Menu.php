<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Menu
 * 
 * @property int $id
 * @property int $parentid
 * @property string $description
 * @property string $path
 * @property int $customertypes
 * @property int $sortnumber
 * 
 * @property \App\Models\Menu $menu
 * @property \Illuminate\Database\Eloquent\Collection $menus
 * @property \Illuminate\Database\Eloquent\Collection $menu_i18ns
 *
 * @package App\Models
 */
class Menu extends Eloquent
{
	protected $table = 'menu';
	public $timestamps = false;

	protected $casts = [
		'parentid' => 'int',
		'customertypes' => 'int',
		'sortnumber' => 'int'
	];

	protected $fillable = [
		'parentid',
		'description',
		'path',
		'customertypes',
		'sortnumber'
	];

	public function menu()
	{
		return $this->belongsTo(\App\Models\Menu::class, 'parentid');
	}

	public function menus()
	{
		return $this->hasMany(\App\Models\Menu::class, 'parentid');
	}

	public function menu_i18ns()
	{
		return $this->hasMany(\App\Models\MenuI18n::class, 'id');
	}
}
