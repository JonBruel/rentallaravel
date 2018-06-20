<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Category
 * 
 * @property int $id
 * @property string $category
 * 
 * @property \Illuminate\Database\Eloquent\Collection $contracts
 *
 * @package App\Models
 */
class Category extends Eloquent
{
	public $timestamps = false;

	protected $fillable = [
		'category'
	];

	public function contracts()
	{
		return $this->hasMany(\App\Models\Contract::class, 'categoryid');
	}
}
