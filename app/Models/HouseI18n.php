<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;
use App\Traits\CompositeKey;

/**
 * Class HouseI18n
 * 
 * @property int $id
 * @property string $culture
 * @property string $description
 * @property string $shortdescription
 * @property string $veryshortdescription
 * @property string $route
 * @property string $carrental
 * @property string $conditions
 * @property string $plan
 * @property string $gallery
 * @property string $keywords
 * @property string $seo
 * @property string $nature
 * @property string $sports
 * @property string $shopping
 * @property string $environment
 * @property string $weather
 * 
 * @property \App\Models\House $house
 *
 * @package App\Models
 */
class HouseI18n extends BaseModel
{
    use CompositeKey;
    protected $table = 'house_i18n';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

    protected $primaryKey = ['id', 'culture'];

	protected $fillable = [
		'description',
		'shortdescription',
		'veryshortdescription',
		'route',
		'carrental',
		'conditions',
		'plan',
		'gallery',
		'keywords',
		'seo',
		'nature',
		'sports',
		'shopping',
		'environment',
		'weather'
	];

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'id');
	}
}
