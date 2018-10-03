<?php

namespace App\Models\Filters;
use Schema;
use Illuminate\Database\Query\Builder;
use Auth;
use App\Models\House;

class ErrorlogFilter extends BaseFilter
{

    public function setup()
    {
        static::$owneridexists = false;
        parent::setup();
        return $this;
    }

    public function created_at($created_at)
    {
        return $this->whereDate('created_at', '<',  $created_at);
    }

    public function stack($searchtext)
    {
        return $this->where('stack', 'LIKE',  '%'.$searchtext.'%');
    }

    public function customermessage($searchtext)
    {
        return $this->where('customermessage', 'LIKE',  '%'.$searchtext.'%');
    }

    public function situation($searchtext)
    {
        return $this->where('situation', 'LIKE',  '%'.$searchtext.'%');
    }

}
