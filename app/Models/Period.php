<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


/**
 * Class Period
 * 
 * @property int $id
 * @property int $year
 * @property int $weeknumber
 * @property string $enddays
 * @property \Carbon\Carbon $from
 * @property \Carbon\Carbon $to
 * @property string $theme
 * @property int $houseid
 * @property int $ownerid
 * @property float $baseprice
 * @property int $basepersons
 * @property int $maxpersons
 * @property float $personprice
 * @property string $extra1
 * @property string $extra2
 * 
 * @property \App\Models\House $house
 * @property \App\Models\Customer $customer
 * @property \Illuminate\Database\Eloquent\Collection $contractlines
 *
 * @package App\Models
 */
class Period extends BaseModel
{
	protected $table = 'period';
	public $timestamps = false;

    public function modelFilter()
    {
        return $this->provideFilter(Filters\PeriodFilter::class);
    }

	protected $casts = [
		'year' => 'int',
		'weeknumber' => 'int',
		'houseid' => 'int',
		'ownerid' => 'int',
		'baseprice' => 'float',
		'basepersons' => 'int',
		'maxpersons' => 'int',
		'personprice' => 'float'
	];

	protected $dates = [
		'from',
		'to'
	];

	protected $fillable = [
		'year',
		'weeknumber',
		'enddays',
		'from',
		'to',
		'theme',
		'houseid',
		'ownerid',
		'baseprice',
		'basepersons',
		'maxpersons',
		'personprice',
		'extra1',
		'extra2'
	];

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function contractlines()
	{
		return $this->hasMany(\App\Models\Contractline::class, 'periodid');
	}
}
