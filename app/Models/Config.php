<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;



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
class Config extends BaseModel
{
	protected $table = 'config';
	public $timestamps = false;

    public function modelFilter()
    {
        return $this->provideFilter(Filters\ConfigFilter::class);
    }

	protected $fillable = [
		'url',
		'index',
		'skin'
	];
}
