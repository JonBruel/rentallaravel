<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Number;

/**
 * Class Contract
 * 
 * @property int $id
 * @property int $houseid
 * @property int $ownerid
 * @property int $customerid
 * @property int $persons
 * @property string $theme
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $landingdatetime
 * @property \Carbon\Carbon $departuredatetime
 * @property string $message
 * @property float $duration
 * @property float $price
 * @property float $discount
 * @property float $finalprice
 * @property int $currencyid
 * @property int $categoryid
 * @property string $status
 * 
 * @property \App\Models\Category $category
 * @property \App\Models\Customer $customer
 * @property \App\Models\Currency $currency
 * @property \App\Models\House $house
 * @property \Illuminate\Database\Eloquent\Collection $accountposts
 * @property \Illuminate\Database\Eloquent\Collection $batchlogs
 * @property \Illuminate\Database\Eloquent\Collection $contractlines
 *
 * @package App\Models
 */
class Contract extends BaseModel
{
	protected $table = 'contract';

    public function modelFilter()
    {
        return $this->provideFilter(Filters\ContractFilter::class);
    }

	protected $casts = [
		'houseid' => 'int',
		'ownerid' => 'int',
		'customerid' => 'int',
		'persons' => 'int',
		'duration' => 'float',
		'price' => 'float',
		'discount' => 'float',
		'finalprice' => 'float',
		'currencyid' => 'int',
		'categoryid' => 'int'
	];

	protected $dates = [
		'landingdatetime',
		'departuredatetime'
	];

	protected $fillable = [
		'houseid',
		'ownerid',
		'customerid',
		'persons',
		'theme',
		'landingdatetime',
		'departuredatetime',
		'message',
		'duration',
		'price',
		'discount',
		'finalprice',
		'currencyid',
		'categoryid',
		'status'
	];

    public function getFinalpriceAttribute($value) {
        if (static::$ajax) return $value;
        return Number::format($value, ['minimum_fraction_digits' => 2, 'maximum_fraction_digits' => 2]);
    }

    public function setFinalpriceAttribute($value) {
        if (static::$ajax) $this->attributes['finalprice'] = $value;
        else $this->attributes['finalprice'] = Number::parse($value);
    }


    public function category()
	{
		return $this->belongsTo(\App\Models\Category::class, 'categoryid');
	}

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class, 'currencyid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function accountposts()
	{
		return $this->hasMany(\App\Models\Accountpost::class, 'contractid');
	}

	public function batchlogs()
	{
		return $this->hasMany(\App\Models\Batchlog::class, 'contractid');
	}

	public function contractlines()
	{
		return $this->hasMany(\App\Models\Contractline::class, 'contractid');
	}

	/*
	 * Add a period determined by the periodid.
	 * Retuns 0 if OK.
	 * Return 1 if the period is alreadu occupied.
	 */
	public function addWeek(int $periodid)
    {
        if (Contractline::where('periodid', $periodid)->get()->count() > 0) return 1;

        $contractline = new Contractline();
        $contractline->contractid = $this->id;
        $contractline->periodid = $periodid;
        $contractline->save();
        return 0;
    }
}
