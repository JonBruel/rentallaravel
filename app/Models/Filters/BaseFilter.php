<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;
use Auth;
use Schema;

class BaseFilter extends ModelFilter
{

    protected static $owneridexists = true;

    public function setup()
    {
        //General scope limit to owner of house. If the field ownerid does not exist, NO exception is thrown.
        if ((config('user.role', 1000) >= 10) && (config('app.restrictscopetoowner', -1) > -1)) {
            if(static::$owneridexists) $this->where('ownerid', config('app.restrictscopetoowner', -1));
            static::$owneridexists = true;
        }
        return $this;
    }

    public function name($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('name', 'LIKE', "%$name%");
        });
    }

    public function ownerid($id)
    {
        return $this->where('ownerid', $id);
        //return $this;
    }


}
