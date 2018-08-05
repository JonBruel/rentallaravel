<?php

namespace App\Models\Filters;

use EloquentFilter\ModelFilter;
use Auth;

class BaseFilter extends ModelFilter
{
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
    }
}
