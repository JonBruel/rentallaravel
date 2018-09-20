<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 10:19:01 +0000.
 */

namespace App\Models;

use Carbon\Carbon;


/**
 * Class Currency
 * 
 * @property int $id
 * @property string $currencyname
 * @property string $currencysymbol
 * @property float $rate
 * @property bool $listed
 * @property int $code
 * 
 * @property \Illuminate\Database\Eloquent\Collection $accountposts
 * @property \Illuminate\Database\Eloquent\Collection $contracts
 * @property \Illuminate\Database\Eloquent\Collection $cultures
 * @property \Illuminate\Database\Eloquent\Collection $currencyrates
 * @property \Illuminate\Database\Eloquent\Collection $houses
 *
 * @package App\Models
 */
class Currency extends BaseModel
{
	public $timestamps = false;

	protected $casts = [
		'rate' => 'float',
		'listed' => 'bool',
		'code' => 'int'
	];

	protected $fillable = [
		'currencyname',
		'currencysymbol',
		'rate',
		'listed',
		'code'
	];

	public function accountposts()
	{
		return $this->hasMany(\App\Models\Accountpost::class, 'customercurrencyid');
	}

	public function contracts()
	{
		return $this->hasMany(\App\Models\Contract::class, 'currencyid');
	}

	public function cultures()
	{
		return $this->hasMany(\App\Models\Culture::class, 'currencyid');
	}

	public function currencyrates()
	{
		return $this->hasMany(\App\Models\Currencyrate::class, 'currencyid');
	}

	public function houses()
	{
		return $this->hasMany(\App\Models\House::class, 'currencyid');
	}

	/*
	 * Get the rate used when the contract was committed. If no contract, this
	 * is the case is we ask for the rate before a contract is committed, we
	 * get the most present rate. All rates ae agsinst the Euro.
	 */
    public function getRate($contractid = null)
    {
        $date = Carbon::now();

        //If contractid given, we find the rate at the time of commiting the contract
        if ($contractid)
        {
            //Find the date for the commitment
            $accountpost = Accountpost::where('contractid', $contractid)->where('posttypeid', 10)->where('passifiedby', 0)->first();
            if ($accountpost) $date = $accountpost->created_at;
        }

        //id of 1 is the base currency, Euro
        if ($this->id == 1) return 1;

        $query = Currencyrate::where('currencyid', $this->id)->orderBy('created_at', 'desc');
        $rate2 = $query->where('created_at', '<=', $date)->first();

        //We should have a $rate2, but in the case where no rates were saved for the
        //specific currency prior to $date, this will be null.
        if ($rate2) return $rate2->rate;

        //This will not happen if rates have been recorded properly. But in the case of
        //missing $rate2, where fetch the first rate after the $date
        $query = Currencyrate::where('currencyid', $this->id)->orderBy('created_at', 'asc');
        $rate1 = $query->where('created_at', '>=', $date)->first();
        return $rate1->rate;
    }
}
