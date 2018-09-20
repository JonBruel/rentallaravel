<?php namespace App\Models\Filters;

use EloquentFilter\ModelFilter;
use Illuminate\Session;
use Auth;

class ContractoverviewFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    /*
     * Usually, when this table is used, the defaultHouse will be set.
     * We may return an error if it is not, not implemented yet.
     */
    public function setup()
    {
        if (config('user.role', 1000) >= 10)
        {
            if (config('user.ownerid') > -1) $this->where('ownerid', config('user.ownerid'));
        }
        if (session('defaultHouse' , -1) != -1) return $this->where('houseid', session('defaultHouse'));
        return $this;
    }

    public function year($year = null)
    {
        if ($year == null) $year = date('Y');
        return $this->whereYear('from', $year);
    }

    public function yearfrom($year = null)
    {
        if ($year == null) $year = date('Y');
        return $this->whereYear('from', '>=', $year);
    }

    public function houseid($houseid)
    {
        return $this->where('houseid', $houseid);
    }

    public function ownerid($ownerid)
    {
        return $this->where('ownerid', $ownerid);
    }

}
