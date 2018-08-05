<?php namespace App\Models\Filters;



class HouseFilter extends BaseFilter
{

    public function setup()
    {
        //if (session('defaultHouse' , -1) != -1) return $this->where('houseid', session('defaultHouse'));
        return $this;
    }


    public function address1($address1)
    {
        return $this->where('address1', 'LIKE', "%$address1%");
    }


    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
}
