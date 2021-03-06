<?php

namespace App\Models\Filters;
use Schema;
use Illuminate\Database\Query\Builder;

class HouseFilter extends BaseFilter
{

    public function setup()
    {
        parent::setup();
        return $this;
    }


    public function address1($address1)
    {
        return $this->where('address1', 'LIKE', "%$address1%");
    }

    public function houseid($id)
    {
        return $this->where('id', $id);
    }

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
}
