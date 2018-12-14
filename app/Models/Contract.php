<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Number;
use Carbon\Carbon;
use App\Events\ContractUpdated;
use DB;

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

    public $sortable = [
        'id',
        'customerid',
        'updated_at',
        'ownerid',
    ];

    public $rules = [
        'persons' => ['required', 'between:1,101'],
    ];

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

    static $activefields = [
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

    protected $dispatchesEvents = [
        'updated' => ContractUpdated::class,
        'created' => ContractUpdated::class,
    ];

    public function getFinalpriceAttribute($value) {
        return $this->getNumberAttribute($value, 2);
    }

    public function setFinalpriceAttribute($value) {
        $this->setNumberAttribute($value, 'finalprice');
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
            /**/
            case 'houseid':
                return $this->house->name;
            case 'ownerid':
                return $this->owner->name;
            case 'customerid':
                return $this->customer->name;
            case 'categoryid':
                return $this->category->name;
            case 'curencyid':
                return $this->currency->currencysymbol;
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
            /**/
            case 'customertypeid':
                return  Customertype::all()->pluck('customertype', 'id')->toArray();
            case 'ownerid':
                return Customer::filter()->where('customertypeid', 10)->pluck('name', 'id')->toArray();
            //case 'customerid':
            //    return Customer::filter()->where('customertypeid', 1000)->where('ownerid', $this->ownerid)->pluck('name', 'id')->toArray();
            case 'cultureid':
                return  Culture::all()->pluck('culturename', 'id')->toArray();

            default:
                return null;
        }
    }

    public function category()
	{
		return $this->belongsTo(\App\Models\Category::class, 'categoryid');
	}

	public function customer()
	{
		return $this->belongsTo(\App\Models\Customer::class, 'customerid');
	}

    public function owner()
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
        if (Contractline::where('periodid', $periodid)->where('contractid', '<>', $this->id)->get()->count() > 0) return 1;

        $contractline = new Contractline();
        $contractline->contractid = $this->id;
        $contractline->periodid = $periodid;
        $contractline->save();
        return 0;
    }

    /*
     *  CommitOrder is the central static method for the control of the accountposts and
     *  as such for the initialization of the workflow.
     *
     *  It can be used outside the Contract, and the $contractid is a parameter, which defaults to 0.
     *  As an example sending password reset mails is an action not related to a contract, so we set
     *  the contractid to 0.
     *
     *   Meaning of posttypes, ordered by id:
     *
     *   id     text                                    comments
     *     0    Default posttype	                    By administrator
     *    10	Order committed	                        By system, admin or customer
     *    20	Owners acceptance of order	            Used to trigger email (presently not used)
     *    21	Owners non-acceptance of order	        Used to trigger email passify order
     *    40	Reservation cancelled, missing payment	Only admins. may reduce committed orders
     *    50	Prepayment	                            Triggers receipt mail to customer
     *    60	First reminder arr./dep. times send	    For housekeeping, triggers next reminder
     *    70	Cancelled by administrator	            For orders cancelled, customer cannot cancel
     *    80	Check final payment	                    Registration of payment reminder
     *    90	Arrival and departure time entered	    Done by end user or administrator
     *    100	Final payment received	                Registered by administrator
     *    110	Order changed	                        Changes only permissible by administrator
     *    120	Customer created	                    Can be created by customer or administrator
     *    130	Customer updated	                    By administrator or customer
     *    140	Changes under progress	                Converted to 130 by cron process
     *    150	Final information sent to customer	    To be used when house details are sent
     *    200	Customer asked for password	            Usually by customer
     *    300	Rounding and currency adjustments	    Used for minor ajustment
     *
     */
     public static function commitOrder($posttypeid, $userid, $contractid = 0, $customerid) {

        static::$ajax = true;

        //The name of posttypes are stored in an array
        foreach (Posttype::all() as $posttype) $text[$posttype->id] = $posttype->posttype;
        $moretodo = true;
        $customer = Customer::Find($customerid);

        //The object first part is to determine the currency to be used for the account records
        //We find the first house related to the non-end user type of user.
        //For end users, no house is related to system messages!
        // Case 1: $contractid does not refer to a contract
        if ($contractid == 0) {
            $houseid = 0;
            //Case 1a: Customer with id=$customerid is an end user
            if ($customer->customertypeid = 1000)
            {
                $house = House::where('ownerid', Customer::Find($customerid)->ownerid)->first();
                if ($house) $houseid = $house->id;
            }
            //Case 1b: Customer with id=$customerid is NOT an end user
            else
            {
                $ownerid = $customer->ownerid;
                //If $ownerid is zero we do a final attempt to find it based on restrictscopetoowner.
                if ((config('app.restrictscopetoowner')) && ($ownerid == 0)) {
                    $owners = explode(';', config('app.restrictscopetoowner'));
                    if (sizeof($owners) > 1)
                        $ownerid = $owners[0];
                    else
                        $ownerid = config('app.restrictscopetoowner', 0);
                }
                $house = House::where('ownerid', $ownerid)->first();
                if ($house) $houseid = $house->getId();
            }
            //Find default customer currency
            $culture = Culture::Find($customer->cultureid);
            $customercurrencyid = $culture->currencyid;
        }
        // Case 2: $contractid refers to a real contract
        else
        {
            $contract = Contract::Find($contractid);
            $customercurrencyid = $contract->currencyid;
            $ownerid = $contract->ownerid;
            $houseid = $contract->houseid;
        }
        //Accountcurrency below is the currency of the owner, this will
        //typically be the same currency as for the bank account of the owner.
        $owner = Customer::Find($ownerid);

        //Below first part completed, we have chosen an currency for the account post, and we can find the rate of today.
         $accountcurrency = $owner->culture->currency;

        $accountcurrencyid = $accountcurrency->id;

        $accountcurrencyrate = Currencyrate::where('currencyid', $accountcurrencyid)->where('created_at', '<=', Carbon::now())->orderBy('created_at', 'desc')->first()->rate;


        //The currency used in the contract is as default the customers preferred currency
        //but the customer may change it before committing.
        $customercurrency = Currency::Find($customercurrencyid);
        $customercurrencyrate = Currencyrate::where('currencyid', $accountcurrencyid)->where('created_at', '<=', Carbon::now())->orderBy('created_at', 'desc')->first()->rate;
        $customercurrencyid = $customercurrency->id;
        $usedrate = round($accountcurrencyrate / $customercurrencyrate, 8);

        //Commitment
        if ($posttypeid == 10) {
            $moretodo = false;

            //We check for previous cancelled commitments and recommit them and de-passify them
            //40: Reservation cancelled, missing payment OR 70: Cancelled by administrator
            $accountpost = Accountpost::where('contractid', $contractid)->whereIn('posttypeid', [40, 70])->orderBy('id', 'desc')->first();

            if ($accountpost) {
                $oldcommitment = Accountpost::where('passifiedby', $accountpost->id)->where('posttypeid', 10)->orderBy('id', 'desc')->first();
                if ($oldcommitment) {
                    $oldcommitment->passifiedby = 0;
                    $oldcommitment->text = 'Order re-committed';
                    $oldcommitment->save();

                    $accountpost->passifiedby = $oldcommitment->id;
                    $accountpost->text = 'Cancelled after order re-committed';
                    $accountpost->amount = 0;
                    $accountpost->returndate = $contract->getReturndate();
                    if (!$accountpost->save()) die(var_dump($accountpost->errors));

                    $contract->status = 'Committed';
                    $contract->customerid = $customerid;
                    $contract->save();
                    static::$ajax = false;
                    return 'Order re-committed';
                }
            }

            //Calculate stored final price in owners currency
            $finalprice = $contract->finalprice * $usedrate;

            //Check if already saved, only proceed if contract not already committed
            if ($contract->status == 'Committed')
            {

                $message = "Already committed";
                //For repair reasons, we set the used rate
                //Record will be updated, there should only be one record!
                //Added by jbr 2-9-2018: posttypeid and passifiedby requirements
                foreach (Accountpost::where('contractid', $contractid)->where('posttypeid', 10)->where('passifiedby', 0)->get() as $accountpost)
                {
                    $accountpost->amount = $finalprice;
                    $accountpost->currencyid = $accountcurrencyid;
                    $accountpost->customercurrencyid = $customercurrencyid;
                    $accountpost->usedrate = $usedrate;
                    $accountpost->returndate = $contract->getReturndate();
                    if (!$accountpost->save()) die(var_dump($accountpost->errors));
                }
            }
            //New accountpost is created
            else
            {
                $accountpost = new Accountpost();

                $accountpost->customerid = $contract->customerid;
                $accountpost->ownerid = $ownerid;
                $accountpost->houseid = $houseid;
                $accountpost->postsource = 'method commitOrder';
                $accountpost->amount = $finalprice;
                $accountpost->currencyid = $accountcurrencyid;
                $accountpost->customercurrencyid = $customercurrencyid;
                $accountpost->usedrate = $usedrate;
                $accountpost->text = $text[$posttypeid];
                $accountpost->contractid = $contractid;
                $accountpost->posttypeid = $posttypeid;
                $accountpost->postedbyid = $userid;
                $accountpost->passifiedby = 0;
                $accountpost->returndate = $contract->getReturndate();
                if (!$accountpost->save()) die(var_dump($accountpost->errors));
                $contract->status = 'Committed';
                $contract->save();
                $message = "Order committed OK";
            }
        }

         // Handling of changes (silent changes, to be converted to announced changes, posttypeid 110, by cron)
         // The cron activates the task: deletecontractorphans
         // Posttyieid 140: Changes under progress. This type will be caught the a cron process and converted to
         // 130, whereby an E-mail will be sent out. The rationale behind this is to allow the user to make changes
         // without getting a mail every time. So the conversion is delayed and requires stable records for some minutes.
         if ($posttypeid == 140) {
            $moretodo = false;
            //Calculate the stored price in owners currency
            $contractprice = $contract->finalprice * $usedrate;
            $previousprice = 0;

            //Calculate the price change
            foreach (Accountpost::where('contractid', $contractid)->whereIn('posttypeid', [10,110])->where('passifiedby', 0)->get()
                     as $accountpost) $previousprice += $accountpost->amount;

            $pricechange = $contractprice - $previousprice;

            //Check if the change is above threshold:

            if ($contractprice != 0) $compareto = $contractprice;
            else  $compareto = 1;

            //Check if we want to register the change, small changes are neglected
            $savechange = false;
            $relativechange = abs($pricechange / $compareto);
            if ($relativechange > 0.001) $savechange = true;

            //Check if a silent change record already exists, delete id and create a new
            Accountpost::where('contractid', $contractid)->where('posttypeid', 140)->where('passifiedby', 0)->delete();

            if ($savechange)
            {
                $accountpost = new Accountpost();
                $accountpost->customerid = $contract->customerid;
                $accountpost->ownerid = $ownerid;
                $accountpost->houseid = $houseid;
                $accountpost->postsource = 'method commitOrder()';
                $accountpost->amount = $pricechange;
                $accountpost->currencyid = $accountcurrencyid;
                $accountpost->customercurrencyid = $customercurrencyid;
                $accountpost->usedrate = $usedrate;
                $accountpost->text = $text[$posttypeid];
                $accountpost->contractid = $contractid;
                $accountpost->posttypeid = $posttypeid;
                $accountpost->postedbyid = $userid;
                $accountpost->passifiedby = 0;
                $accountpost->returndate = $contract->getReturndate();
                $accountpost->save();
                $message = "Order adjusted OK";
            }
            else $message = "No adjustments made";

            $contract->status = 'Committed';
            $contract->save();
        }


        //Cancellation
        if (($posttypeid == 70) OR ( $posttypeid == 40)) {
            // 40: Cancelled by the user, 70: Cancelled by the owner
            // We determine the last date for customer payments, if any
            $idstart = 0;
            $payments = Accountpost::where('contractid', $contractid)->whereIn('posttypeid', [50, 110])->orderBy('id', 'desc')->first();
            if ($payments) $idstart = $payments->id + 1;


            //We detimine the contract sum, to be used for the new posttype 70 record
            $moretodo = false;
            $nonpassifiedsum = 0;
            foreach (Accountpost::where('contractid', $contractid)->where('passifiedby', 0)->where('id', '>=', $idstart)->get() as $accountpost)
            {
                $nonpassifiedsum += $accountpost->getAmount($idstart);
            }

            //We insert a new accountpost, we copy the stuff from:
            // the last committed order or order-change after the last payment (i.e. id>=$idstart)
            //in order to get the used currencies and rates
            $oldaccountpost = Accountpost::where('contractid', $contractid)->whereIn('posttypeid', [10, 110, 140])->where('id', '>=', $idstart)
                                        ->orderBy('created_at', 'desc')->get();

            if ($oldaccountpost)
            {
                $accountpost = new Accountpost();
                foreach (static::$activefields as $field) $accountpost->$field = $oldaccountpost->$field;
                $accountpost->amount = -$nonpassifiedsum;
                $accountpost->posttypeid = $posttypeid;
                $accountpost->text = 'Cancelling previous posts';

                $accountpost->save();
                $newid = $accountpost->id;

                //We passify the old posts related to the contractid
                //*** We may want to modify this method and/or to make it a method belonging to Accountposts....
                foreach (Accountpost::where([['id', '<', $newid], ['id', '>=', $idstart]])->where('contractid', $contractid)->where('passifiedby', 0)->get() as $oldaccountpost)
                {
                    $oldaccountpost->passifiedby = $newid;
                    $oldaccountpost->save();
                }
                $message = "Order uncommitted OK";
            }
            $contract->status = 'Uncommitted';
            $contract->save();
            $message = "Order uncommitted, no account records changed";
        }

        //Customer registration of arrival time, customer registration, customer change, customer asked for password
        //The common denominator is the amount being 0.
        if (($moretodo)) {
            //New accountpost with contractid=0 is created
            $moretodo = false;
            $accountpost = new Accountpost();

            $accountpost->customerid = $contract->customerid;
            $accountpost->ownerid = $ownerid;
            $accountpost->houseid = $houseid;
            $accountpost->postsource = 'queuesystem';
            $accountpost->amount = 0;
            $accountpost->currencyid = $accountcurrencyid;
            $accountpost->customercurrencyid = $customercurrencyid;
            $accountpost->usedrate = 1;
            $accountpost->text = $text[$posttypeid];
            $accountpost->contractid = $contractid;
            $accountpost->posttypeid = $posttypeid;
            $accountpost->postedbyid = 0;
            $accountpost->passifiedby = 0;
            $accountpost->returndate = $contract->getReturndate();
            $accountpost->save();
            $newid = $accountpost->id;
            $message = "Created " . $text[$posttypeid];
            foreach (Accountpost::where('id', '<', $newid)->where('contractid', $contractid)->where('passifiedby', 0)
                     ->where('customerid', $customerid)->where('posttypeid', $posttypeid)->get() as $oldaccountpost) {
                //To be changed, type 10 must exist if total ...
                $oldaccountpost->passifiedby = $newid;
                $oldaccountpost->save();
            }
        }
        static::$ajax = false;
        return $message;
    }

    //Get relevant accountposts
    public function getAccountposts($culture = '') {
        static::$ajax = true;
        $sum = 0;
        $r = '<table class="table">';
        $customercurrencysymbol = $this->convertCurrencyAccountToCustomer('currencysymbol');
        $usedrate = $this->convertCurrencyAccountToCustomer();
        //die('Used rate: '. $usedrate);
        if ($usedrate == 0) $usedrate = 1;
        $first = true;
        $columns = array('created_at' => 'Date', 'text' => 'Text', 'amount' => 'Movement');
        foreach (Accountpost::where('contractid', $this->id)->where('amount' ,'<>', 0)->get() as $accountpost) {
            if ($first) {
                $r .= '<tr>';
                foreach ($columns as $key => $value) {
                    $listedvalue = __($value);
                    if ($key == 'amount')
                        $listedvalue = $customercurrencysymbol;
                    $r .= '<th>&nbsp;' . $listedvalue . '&nbsp;&nbsp;&nbsp;</th>';
                }
                $r .= '</tr>';
                $first = false;
            }
            if (!($first)) {
                $r .= '<tr>';
                foreach ($columns as $key => $value) {
                    $listedvalue = $accountpost->$key;
                    if ($key == 'created_at')
                        $listedvalue = $listedvalue->format('Y-m-d');
                    if ($key == 'text')
                    {
                        $listedvalue = __($listedvalue, [], $culture);
                    }
                    if ($key == 'amount') {
                        $sum = $sum - ($listedvalue / $usedrate);
                        $listedvalue = static::format(-$listedvalue / $usedrate, 2);
                    }
                    $r .= '<td>&nbsp;&nbsp;' . $listedvalue . '&nbsp;&nbsp;&nbsp;</td>';
                }
                $r .= '</tr>';
            }
        }
        if (abs($sum) < 0.0049)
            $sum = 0;
        $r .= '<tr><td></td><td>&nbsp;&nbsp;' . __('Balance', [], $culture) . ': </td><td>&nbsp;&nbsp;' . static::format($sum, 2, $culture) . '&nbsp;&nbsp;&nbsp;</td></tr>';
        $r .= '</table>';
        static::$ajax = false;
        return $r;
    }

    //We want to express the accountposts in customers currency - as chosen in the contract,
    //but they are stored in accountposts in houseowners currency. So we need to convert.
    //The conversion should be at the rate used at the (last) committed purchase
    function convertCurrencyAccountToCustomer($amount = null) {

        if ($amount == 'currencysymbol') return $this->currency->currencysymbol;

        $usedrate = 1;
        //The rate is the rate of the time of the committment
        if ($this->id > 0)
        {
            $accountidbalance = DB::table('accountidbalance')->Find($this->id);
            if ($accountidbalance) $usedrate = $accountidbalance->usedrate;
        }
        return $usedrate;
    }

    //We want to express prices from the period in customers currency - as chosen in the contract,
    //but they are stored in periods in the currency the house.
    //The rate used, is the rate at the time of the (last) commitment, as in the function above.
    function convertCurrencyPeriodToCustomer($amount = null) {

        $contractid = $this->id;
        if ($amount == 'currencysymbol') return $this->currency->currencysymbol;

        $customerrate = $this->currency->getRate($contractid);

        $house = House::Find($this->houseid);
        $housecurrencyrate = $house->currency->getRate($contractid);

        $usedrate = $customerrate/$housecurrencyrate;

        return $usedrate;
    }

    /*
     * Removes contracts with status "New" which are modified earlier than $timeout seconds ago.
     */
    public static function removeOldNew($timeout = 300)
    {
        $notBefore = Carbon::now()->subSeconds($timeout);
        Contract::where('status', 'New')->where('updated_at', '<', $notBefore)->delete();
    }

    public function getArrival($culture = '')
    {
        if ($this->landingdatetime) return static::formatDateTimeToString($this->landingdatetime, $culture);
        return __('Not known', [], $culture);
    }

    public function getDeparture($culture = '')
    {
        if ($this->departuredatetime) return static::formatDateTimeToString($this->departuredatetime, $culture);
        return __('Not known', [], $culture);
    }


    public function getOrder($culture)
    {
        $sum = 0;
        $r = '';
        $customercurrencysymbol = $this->convertCurrencyPeriodToCustomer('currencysymbol');
        $usedrate = $this->convertCurrencyPeriodToCustomer();

        $first = true;
        $columns = array('from' => 'From', 'to' => 'To', 'baseprice' => 'Price', 'persons' => 'Persons', 'personsprice' => 'Extra person price', 'total' => 'Total in');
        $r .= '<table>';

        foreach (Contractline::where('contractid', $this->id)->get() as $contractline) {
            $period = $contractline->period;
            if ($first) {
                $r .= '<tr>';
                foreach ($columns as $key => $value) {
                    $listedvalue = __($value);
                    if ($key == 'total')
                        $listedvalue = $listedvalue . ' ' . $customercurrencysymbol;
                    $r .= '<th>&nbsp;' . $listedvalue . '&nbsp;&nbsp;&nbsp;</th>';
                }
                $r .= '</tr>';
                $first = false;
            }
            if (!($first)) {
                $r .= '<tr>';
                foreach ($columns as $key => $value) {
                    if (($key == 'from') OR ( $key == 'to'))
                        $listedvalue = static::formatDateToString($period->$key, $culture);
                    if ($key == 'baseprice')
                        $listedvalue = static::format($usedrate * ($bp = $period->baseprice), 2, $culture);
                    if ($key == 'persons')
                        $listedvalue = $this->persons;
                    if ($key == 'personsprice')
                        $listedvalue = static::format($usedrate * ($pp = $period->personprice * max(0, ($this->persons - $period->basepersons))), 2, $culture);
                    if ($key == 'total')
                        $listedvalue = static::format($usedrate * ($bp + $pp), 2, $culture);
                    $r .= '<td align="right">&nbsp;&nbsp;' . $listedvalue . '</td>';
                }
                $r .= '</tr>';
                $sum += $bp + $pp;
            }
        }
        if ($this->discount > 0) {
            $r .= '<tr><td colspan="3"></td>';
            $r .= '<td>&nbsp;&nbsp;Discount: ' . static::format($this->discount, 2, $culture) . '% </td>';
            $r .= '<td>&nbsp;&nbsp;' . static::format($usedrate * ($sum * $this->discount / 100), 2, $culture) . ' </td></tr>';
        }
        $r .= '<tr><td colspan="4"></td><th>&nbsp;&nbsp;' . __('Final price') . ':</th>';
        $r .= '<th class="currencysum">&nbsp;&nbsp;' . $this->format($usedrate * $sum * (1 - $this->discount / 100), 2, $culture) . ' </th></tr>';
        $r .= '</table>';
        return $r;
    }

    public function getPeriodtext($culture)
    {
        $contractlines = Contractline::where('contractid', $this->id)->get();
        if (sizeof($contractlines) > 0) {
            $from = $contractlines[0]->period->from;
            $to = $contractlines[0]->period->to;
            foreach ($contractlines as $contractline)
            {
                if ($from->gt($contractline->period->from)) $from = $contractline->period->from;
                if ($to->lt($contractline->period->to)) $to = $contractline->period->to;
            }
            $periodtext = __('From',[], $culture) . ' ' . static::formatDateToString($from, $culture) . ' ' . __('to', [], $culture) . ' ' . static::formatDateToString($to, $culture);
        }
        return $periodtext;
    }

    public function getReturndate()
    {
        $contractlines = Contractline::where('contractid', $this->id)->get();
        if (sizeof($contractlines) > 0) {
            $to = $contractlines[0]->period->to;
            foreach ($contractlines as $contractline)
            {
                if ($to->lt($contractline->period->to)) $to = $contractline->period->to;
            }
        }
        return $to;
    }

    public function getPrepaymentamount()
    {
        $house = $this->house;
        $prepaymentamount = $house->prepayment * $this->finalprice;
        $currency = $this->currency;
        $currencysymbol = $currency->currencysymbol;
        $r = $currencysymbol . ' ' . static::format($prepaymentamount, 2);
        return $r;
    }
}
