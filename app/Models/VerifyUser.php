<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 25-08-2018
 * Time: 11:25
 */

namespace App\Models;


class VerifyUser extends BaseModel
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'customer_id');
    }
}