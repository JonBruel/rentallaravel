<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;
use Auth;

class CustomerFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function setup()
    {
        if (config('user.role', 1000) == 1000) return $this->where('id', Auth::user()->id);
        if (config('user.role', 1000) == 100) return $this->where('ownerid', Auth::user()->id);
        if (config('user.role', 1000) == 110) return $this->where('ownerid', Auth::user()->ownerid);
        return $this;
    }
}
