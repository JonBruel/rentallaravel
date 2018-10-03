<?php

namespace App\Models\Filters;

use Auth;
use Carbon\Carbon;

class BatchlogFilter extends BaseFilter
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

        if (config('user.role', 1000) < 10) return $this;
        if (config('user.role', 1000) == 10) return $this->where('ownerid', Auth::user()->id);
        if (config('user.role', 1000) == 100) return $this->where('ownerid', Auth::user()->ownerid);
        //Only allowed for owners, supervisors and administrators, below will result en an empty result
        return $this->where('ownerid', -1);
    }

    //$fields = ['created_at', 'statusid', 'posttypeid', 'batchtaskid', 'contractid', 'emailid', 'houseid'];
    public function created_at($date)
    {
        if ($date) return $this->whereDate('created_at', '>', Carbon::parse($date));
        return $this;
    }

    public function posttypeid($id)
    {
        if ($id) return $this->where('posttypeid', $id);
    }

    public function statusid($search)
    {
        if ($search) return $this->where('statusid', $search);
    }

    public function batchtaskid($search)
    {
        if ($search) return $this->where('batchtaskid', $search);
    }

    public function contractid($search)
    {
        if ($search) return $this->where('contractid', $search);
    }

    public function emailid($search)
    {
        if ($search) return $this->where('emailid', $search);
    }

    public function houseid($search)
    {
        if ($search) return $this->where('houseid', $search);
    }
}
