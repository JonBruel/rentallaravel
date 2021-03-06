<?php namespace App\Models\Filters;

use EloquentFilter\ModelFilter;
use Illuminate\Session;
use Auth;

class PeriodFilter extends ModelFilter
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
        if (session('defaultHouse' , -1) != -1) return $this->where('houseid', session('defaultHouse'));
        return $this;
    }
}
