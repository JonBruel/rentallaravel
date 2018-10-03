<?php

namespace App\Models\Filters;
use Schema;
use Illuminate\Database\Query\Builder;
use Auth;
use App\Models\House;

class ConfigFilter extends BaseFilter
{

    public function setup()
    {
        static::$owneridexists = false;
        parent::setup();
        if (config('user.role', 1000) == 10)
        {
            $houses = House::where('ownerid', Auth::user()->id)->get();
            $houseurls = [];
            foreach($houses as $house) $houseurls[] = str_replace('https://', '', str_replace('http://', '', $house->www));
            if (sizeof($houseurls) > 0) return $this->whereIn('url', $houseurls);
        }
        if (config('user.role', 1000) > 10)
        {
            return $this->where('id', 0); //Nothing will be returned
        }

        return $this;
    }


    public function url($url)
    {
        return $this->where('url', $url);
    }

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
}
