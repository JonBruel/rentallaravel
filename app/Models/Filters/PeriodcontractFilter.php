<?php namespace App\Models\Filters;

use EloquentFilter\ModelFilter;
use Illuminate\Session;
use Auth;

class PeriodcontractFilter extends ModelFilter
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
        return $this->where('houseid', session('defaultHouse', config('app.default_house')));
    }
}
