<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;
use Number;
use Auth;
use Illuminate\Support\Str;


/**
 * Class Customer
 * 
 * @property int $id
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $country
 * @property string $lasturl
 * @property string $telephone
 * @property string $mobile
 * @property string $email
 * @property string $login
 * @property string $password
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $notes
 * @property int $customertypeid
 * @property int $ownerid
 * @property int $houselicenses
 * @property int $status
 * @property int $cultureid
 * 
 * @property \App\Models\Customertype $customertype
 * @property \App\Models\Customer $customer
 * @property \App\Models\Culture $culture
 * @property \App\Models\Customerstatus $customerstatus
 * @property \Illuminate\Database\Eloquent\Collection $accountposts
 * @property \Illuminate\Database\Eloquent\Collection $batchlogs
 * @property \Illuminate\Database\Eloquent\Collection $batchtasks
 * @property \Illuminate\Database\Eloquent\Collection $bounties
 * @property \Illuminate\Database\Eloquent\Collection $bountyanswers
 * @property \Illuminate\Database\Eloquent\Collection $contracts
 * @property \Illuminate\Database\Eloquent\Collection $customers
 * @property \Illuminate\Database\Eloquent\Collection $emaillogs
 * @property \Illuminate\Database\Eloquent\Collection $houses
 * @property \Illuminate\Database\Eloquent\Collection $periods
 * @property \Illuminate\Database\Eloquent\Collection $standardemails
 * @property \Illuminate\Database\Eloquent\Collection $testimonials
 *
 * @package App\Models
 */
class Customer extends BaseModel
{
    public static $customertypes = ['Test' => 0, 'Supervisor' => 1, 'Owner' => 10, 'Administrator' => 100, 'Personel' => 110, 'Customer' => 1000];

    //$table->integer('id');
    //$table->primary('id');

	public $sortable = [
	    'id',
        'name',
        'address1',
        'address2',
        'address3',
        'country',
        'telephone',
        'ownerid',
        'email',
    ];



    public function modelFilter()
    {
        return $this->provideFilter(Filters\CustomerFilter::class);
    }

    protected $table = 'customer';

	protected $casts = [
		'customertypeid' => 'int',
		'ownerid' => 'int',
		'houselicenses' => 'int',
		'status' => 'int',
		'cultureid' => 'int'
	];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

	protected $fillable = [
		'name',
		'address1',
		'address2',
		'address3',
		'country',
		'lasturl',
		'telephone',
		'mobile',
		'email',
		'login',
		'password',
        'plain_password',
		'notes',
		'customertypeid',
		'ownerid',
		'houselicenses',
		'status',
		'cultureid'
	];


    public $rules = [
        'name' => ['required', 'between:5,51'],
        'address1' => ['required', 'between:3,51'],
        'country' => ['required', 'between:3,51'],
        'mobile' => ['required', 'between:8,21'],
        'email' => ['required', 'unique:customer']
    ];



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
            case 'customertypeid':
                return $this->customertype->customertype;
            case 'ownerid':
                return $this->customer->name;
            case 'cultureid':
                return $this->culture->culturename;
            case 'status':
                return $this->customerstatus->status;
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
            case 'customertypeid':
                return  Customertype::where('id', '>', Auth::user()->customertypeid)->pluck('customertype', 'id')->toArray();
            case 'ownerid':
                return Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
            case 'cultureid':
                return  Culture::all()->pluck('culturename', 'id')->toArray();
            case 'status':
                return  Customerstatus::all()->pluck('status', 'id')->toArray();
            default:
                return null;
        }
    }

	public function customertype()
	{
		return $this->belongsTo(\App\Models\Customertype::class, 'customertypeid');
	}


	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'ownerid');
	}

	public function culture()
	{
		return $this->belongsTo(\App\Models\Culture::class, 'cultureid');
	}

	public function customerstatus()
	{
		return $this->belongsTo(\App\Models\Customerstatus::class, 'status');
	}

	public function accountposts()
	{
		return $this->hasMany(\App\Models\Accountpost::class, 'customerid');
	}

	public function batchlogs()
	{
		return $this->hasMany(\App\Models\Batchlog::class, 'customerid');
	}

	public function batchtasks()
	{
		return $this->hasMany(\App\Models\Batchtask::class, 'ownerid');
	}

	public function bounties()
	{
		return $this->hasMany(\App\Models\Bounty::class, 'userid');
	}

	public function bountyanswers()
	{
		return $this->hasMany(\App\Models\Bountyanswer::class, 'userid');
	}

	public function contracts()
	{
		return $this->hasMany(\App\Models\Contract::class, 'customerid');
	}

	public function customers()
	{
		return $this->hasMany(\App\Models\Customer::class, 'ownerid');
	}

	public function emaillogs()
	{
		return $this->hasMany(\App\Models\Emaillog::class, 'customerid');
	}

	public function houses()
	{
		return $this->hasMany(\App\Models\House::class, 'ownerid');
	}

	public function periods()
	{
		return $this->hasMany(\App\Models\Period::class, 'ownerid');
	}

	public function standardemails()
	{
		return $this->hasMany(\App\Models\Standardemail::class, 'ownerid');
	}

	public function testimonials()
	{
		return $this->hasMany(\App\Models\Testimonial::class, 'userid');
	}

    public function sendPasswordResetNotification($token)
    {
        // Your your own implementation.
        $this->notify(new ResetPasswordNotification($token, $this->getEmailForPasswordReset()));
    }

    public function verifyUser()
    {
        return $this->hasOne('App\Models\VerifyUser', 'customer_id');
    }


    public function getLoginlink($culture = '')
    {
        $remember_token = $this->remember_token;
        if (is_null($remember_token))
        {
            $remember_token = Str::random(60);
            $this->remember_token = $remember_token;
            $this->save();
        }
        $r = $this->lasturl.'/home/tokenlogin?email=' . $this->email . '&remember_token=' . $remember_token . '&redirectTo=/home/showinfo/description';
        $r = '<a href="' . $r . '">'.__('Login').'</a>';
        return $r;
    }

    public function getTestimoniallink($houseid, $culture = '')
    {
        $house = House::Find($houseid);
        $remember_token = $this->remember_token;
        if (is_null($remember_token))
        {
            $remember_token = Str::random(60);
            $this->remember_token = $remember_token;
            $this->save();
        }
        $r = $house->www.'/home/listtestimonials?email=' . $this->email . '&remember_token=' . $remember_token . '&redirectTo=/home/listtestimonials&houseid=' . $houseid.'&menupoint=10080';
        $r = '<a href="' . $r . '">'.__('Your feedback regarding', [], $culture). ' ' . $house->name . '</a>';
        return $r;
    }

    public function getTimelink($contractid, $culture = '')
    {
        $contract = Contract::Find($contractid);
        $house = House::Find($contract->houseid);
        $remember_token = $this->remember_token;
        if (is_null($remember_token))
        {
            $remember_token = Str::random(60);
            $this->remember_token = $remember_token;
            $this->save();
        }

        $r = $house->www.'/myaccount/edittime?contractid='.$contractid.'&email='.$this->email.'&remember_token='.$this->remember_token.'&redirectTo=/myaccount/edittime&contractid='.$contractid.'&menupoint=9050';
        $r = '<a href="' . $r . '">'.__('Our arrival and departure time', [], $culture).'</a>';
        return $r;
    }

    public function getCustomerinfo($culture = '')
    {
        $r = $this->name . "\r";
        $r .= $this->address1 . "\r";
        $r .= $this->address2 . "\r";
        if ($this->country) $r .= '(' . $this->country . ")\r";
        $r .= __('Phone', [], $culture).': ' . $this->telephone . "\r";
        $r .= __('Mobile', [], $culture).': ' . $this->mobile . "\r";
        return $r;
    }

}
