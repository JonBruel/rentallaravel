<?php

namespace App\Models\Filters;

use Auth;

class ContractFilter extends BaseFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    /*
     * The basic filter for viewing customers.
     * When used by supervisor for chosing ownerid, add ->where('customertypeid', 10)
     *
     * For customertypes see Customer:
     * Customer::$customertypes = ['Test' => 0, 'Supervisor' => 1, 'Owner' => 10, 'Administrator' => 100, 'Personel' => 110, 'Customer' => 1000];
     *
     */
    public function setup()
    {
        parent::setup();

        if (config('user.role', 1000) == 10) return $this->where('ownerid', Auth::user()->id)->where('customertypeid', '>', 10);
        if (config('user.role', 1000) == 100) return $this->where('ownerid', Auth::user()->ownerid)->where('customertypeid', '>', 100);
        if (config('user.role', 1000) == 110) return $this->where('ownerid', Auth::user()->ownerid)->where('customertypeid', '>', 110);
        if (config('user.role', 1000) == 1000)
        {
            if (Auth::check()) return $this->where('id', Auth::user()->id);
            else return $this->where('ownerid', -1); //Will cause empty customer list.
        }
        return $this;
    }

}
