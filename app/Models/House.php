<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;
use Carbon\Carbon;



/**
 * Class House
 * 
 * @property int $id
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $country
 * @property string $www
 * @property float $latitude
 * @property float $longitude
 * @property bool $lockbatch
 * @property int $currencyid
 * @property int $ownerid
 * @property string $maidid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $viewfilter
 * @property float $prepayment
 * @property int $disttobeach
 * @property int $maxpersons
 * @property bool $isprivate
 * @property bool $dishwasher
 * @property bool $washingmachine
 * @property bool $spa
 * @property bool $pool
 * @property bool $sauna
 * @property bool $fireplace
 * @property bool $internet
 * @property bool $pets
 * 
 * @property \App\Models\Currency $currency
 * @property \App\Models\Customer $customer
 * @property \App\Models\Customertype $customertype
 * @property \Illuminate\Database\Eloquent\Collection $accountposts
 * @property \Illuminate\Database\Eloquent\Collection $batchlogs
 * @property \Illuminate\Database\Eloquent\Collection $batchtasks
 * @property \Illuminate\Database\Eloquent\Collection $contracts
 * @property \Illuminate\Database\Eloquent\Collection $emaillogs
 * @property \Illuminate\Database\Eloquent\Collection $house_i18ns
 * @property \Illuminate\Database\Eloquent\Collection $periods
 * @property \Illuminate\Database\Eloquent\Collection $standardemails
 * @property \Illuminate\Database\Eloquent\Collection $testimonials
 *
 * @package App\Models
 */
class House extends BaseModel
{
    protected $table = 'house';

    public function modelFilter()
    {
        return $this->provideFilter(Filters\HouseFilter::class);
    }

    public $sortable = [
        'name',
        'address1',
        'address2',
        'address3',
        'country',
        'ownerid',
    ];

    public $casts = [
		'latitude' => 'float',
		'longitude' => 'float',
		'lockbatch' => 'int',
		'currencyid' => 'int',
		'ownerid' => 'int',
		'viewfilter' => 'int',
		'prepayment' => 'float',
		'disttobeach' => 'int',
		'maxpersons' => 'int',
		'isprivate' => 'bool',
		'dishwasher' => 'bool',
		'washingmachine' => 'bool',
		'spa' => 'bool',
		'pool' => 'bool',
		'sauna' => 'bool',
		'fireplace' => 'bool',
		'internet' => 'bool',
		'pets' => 'bool'
	];

	protected $fillable = [
		'name',
		'address1',
		'address2',
		'address3',
		'country',
		'www',
		'latitude',
		'longitude',
		'lockbatch',
		'currencyid',
		'ownerid',
		'maidid',
		'viewfilter',
		'prepayment',
		'disttobeach',
		'maxpersons',
		'isprivate',
		'dishwasher',
		'washingmachine',
		'spa',
		'pool',
		'sauna',
		'fireplace',
		'internet',
		'pets'
	];

    public $rules = [
        'name' => ['required', 'between:3,30'],
        'address1' => ['required', 'between:3,30'],
        'latitude' => ['required', 'between:-180,180', 'numeric'],
        'longitude' => ['required', 'between:-180,180', 'numeric']
    ];

    /**
     * Formats a number.
     *
     * Supported options:
     * - locale:                  The locale. Default: 'en'.
     * - use_grouping:            Whether to use grouping separators,
     *                            such as thousands separators.
     *                            Default: true.
     * - minimum_fraction_digits: Minimum fraction digits. Default: 0.
     * - maximum_fraction_digits: Minimum fraction digits. Default: 3.
     * - rounding_mode:           The rounding mode.
     *                            A PHP_ROUND_ constant or 'none' to skip
     *                            rounding. Default: PHP_ROUND_HALF_UP.
     * - style:                   The style.
     *                            One of: 'decimal', 'percent'.
     *                            Default: 'decimal'.
     *
     * @param string $number  The number.
     * @param array  $options The formatting options.
     *
     * @return string The formatted number.
     */
	public function getLongitudeAttribute($value) {
        if (static::$ajax) return $value;
        return static::format($value,12);
    }

    public function setLongitudeAttribute($value) {
	    if (static::$ajax) $this->attributes['longitude'] = $value;
        else $this->attributes['longitude'] = static::parse($value);
    }

    public function getLatitudeAttribute($value) {
        if (static::$ajax) return $value;
        return static::format($value,12);
    }

    public function setLatitudeAttribute($value) {
        if (static::$ajax) $this->attributes['latitude'] = $value;
	    else $this->attributes['latitude'] = static::parse($value);
    }

    /*
     * This function is used to show the relevant associated
     * user-friendly value as opposed to showing the id.
     * Performance: as we are making up to 4 queries, it does take some time.
     * Measured to around 5 ms.
     */
    public function withBelongsTo($fieldname)
    {
        switch ($fieldname)
        {
            case 'ownerid':
                return $this->customer->name;
            case 'currencyid':
                return $this->currency->currency;
            case 'maidid':
                return $this->maid->name;
            default:
                return $this->$fieldname;
        }
    }

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
            case 'maidid':
                return  Customer::where('ownerid', $this->ownerid)->where('customertypeid', 110)->pluck('name', 'id')->toArray();
            case 'currencyid':
                return Currency::all()->pluck('currencysymbol', 'id')->toArray();
            default:
                return null;
        }
    }



    public function currency()
	{
		return $this->belongsTo(\App\Models\Currency::class, 'currencyid');
	}

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

    public function maid()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'maidid');
    }

    //public function owner()
    //{
    //    return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
    //}

	public function customertype()
	{
		return $this->belongsTo(\App\Models\Customertype::class, 'viewfilter');
	}

	public function accountposts()
	{
		return $this->hasMany(\App\Models\Accountpost::class, 'houseid');
	}

	public function batchlogs()
	{
		return $this->hasMany(\App\Models\Batchlog::class, 'houseid');
	}

	public function batchtasks()
	{
		return $this->hasMany(\App\Models\Batchtask::class, 'houseid');
	}

	public function contracts()
	{
		return $this->hasMany(\App\Models\Contract::class, 'houseid');
	}

	public function emaillogs()
	{
		return $this->hasMany(\App\Models\Emaillog::class, 'houseid');
	}

	public function house_i18ns()
	{
		return $this->hasMany(\App\Models\HouseI18n::class, 'id');
	}

	public function periods()
	{
		return $this->hasMany(\App\Models\Period::class, 'houseid');
	}

	public function standardemails()
	{
		return $this->hasMany(\App\Models\Standardemail::class, 'houseid');
	}

	public function testimonials()
	{
		return $this->hasMany(\App\Models\Testimonial::class, 'houseid');
	}

	public static function copyBatchAndMail($houseid, $overwrite, $cultures = '')
    {
        if ($houseid == 0) return;

        $batchtasks = Batchtask::where('ownerid', 0)->where('houseid', 0)->get();
        if ($cultures == '') $cultures = explode(';', config('app.cultures'));
        $standardemails = Standardemail::where('ownerid', 0)->where('houseid', 0)->get();

        $houses = House::where('id', $houseid)->get();

        foreach ($houses as $house) {
            $ownerid = $house->ownerid;
            $newmailid = [];
            foreach ($standardemails as $standardemail) {
                $standardemailid = $standardemail->id;

                //Get contents of the standard emails
                $standardemailcontents = [];
                foreach ($cultures as $culture)
                {
                    $I18n = StandardemailI18n::where('id', $standardemailid)->where('culture', $culture)->first();
                    $standardemailcontents[$culture] = ($I18n)?$I18n->contents:'';
                }

                $existingemail = Standardemail::where('description', $standardemail->description)->where('houseid', $houseid)->first();
                //We copy the contents of the email belonging to houseid=0 to the new
                if ($existingemail)
                {
                    if ($overwrite == 1) StandardemailI18n::copyContent($existingemail->id, $standardemailcontents);
                }
                //We make a new email
                else
                {
                    $existingemail = new Standardemail(['ownerid' => $ownerid, 'houseid' => $houseid, 'description' => $standardemail->description]);
                    if (!$existingemail->save())
                    {
                        $errors = $existingemail->getErrors();
                        die(var_dump($errors));
                    }
                    StandardemailI18n::copyContent($existingemail->id, $standardemailcontents);
                }
                $newmailid[$standardemailid] = $existingemail->id;
            }
            foreach ($batchtasks as $batchtask)
            {
                $existingbatchtask = Batchtask::where('name', $batchtask->name)->where('houseid', $houseid)->first();
                if ($existingbatchtask)
                {
                    if ($overwrite == 1)
                    {
                        $id = $existingbatchtask->id;
                        foreach ($batchtask->toArray() as $field => $value) $existingbatchtask->$field = $value;
                        $existingbatchtask->id = $id;
                    }
                }
                else
                {
                    $existingbatchtask = new Batchtask();
                    foreach ($batchtask->toArray() as $field => $value) $existingbatchtask->$field = $value;
                    $existingbatchtask->id = null;
                }
                $existingbatchtask->ownerid = $ownerid;
                $existingbatchtask->houseid = $houseid;
                $existingbatchtask->activefrom = Carbon::now();
                $existingbatchtask->emailid = $newmailid[$batchtask->emailid];
                $existingbatchtask->save();
            }
        }
    }
}
