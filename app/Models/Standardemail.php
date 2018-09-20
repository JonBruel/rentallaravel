<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;


/**
 * Class Standardemail
 * 
 * @property int $id
 * @property string $description
 * @property int $ownerid
 * @property int $houseid
 * @property string $extra
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Customer $customer
 * @property \App\Models\House $house
 * @property \Illuminate\Database\Eloquent\Collection $batchtasks
 * @property \Illuminate\Database\Eloquent\Collection $standardemail_i18ns
 *
 * @package App\Models
 */
class Standardemail extends BaseModel
{
	protected $table = 'standardemail';

    public function modelFilter()
    {
        return $this->provideFilter(Filters\StandardemailFilter::class);
    }

	protected $casts = [
		'ownerid' => 'int',
		'houseid' => 'int'
	];

	protected $fillable = [
		'description',
		'ownerid',
		'houseid',
		'extra'
	];

    /*
    * Retuns an array of keys and values to be used in forms for select boxes. Typical uses
    * are filters, e.g selection housed owner by a specific owner.
    *
    * Retuns null if no select boxes are to be used.
    */
    public function withSelect($fieldname)
    {
        switch ($fieldname)
        {
            case 'ownerid':
                return  Customer::where('customertype', 10)->pluck('name', 'id')->toArray();
            case 'houseid':
                return  House::where('ownerid', $this->ownerid)->pluck('name', 'id')->toArray();
            default:
                return null;
        }
    }
	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function house()
	{
		return $this->belongsTo(\App\Models\House::class, 'houseid');
	}

	public function batchtasks()
	{
		return $this->hasMany(\App\Models\Batchtask::class, 'emailid');
	}

	public function standardemail_i18ns()
	{
		return $this->hasMany(\App\Models\StandardemailI18n::class, 'id');
	}
}
