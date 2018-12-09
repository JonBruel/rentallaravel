<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use Notifiable;
    use Impersonate;
    protected $table = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $fillable = [
    //    'name', 'email', 'password', 'customertypeid',
    //];
    protected $fillable = [
        'name',
        'address1',
        'address2',
        'address3',
        'country',
        'lasturl',
        'telephone',
        'mobile',
        'email',
        'login',
        'password',
        'plain_password',
        'notes',
        'customertypeid',
        'ownerid',
        'houselicenses',
        'status',
        'cultureid'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        // Your your own implementation.
        $this->notify(new ResetPasswordNotification($token, $this->getEmailForPasswordReset()));
    }


    /**
     * @return bool
     */
    public function canImpersonate()
    {
        // For example
        return $this->customertypeid <= 10;
    }

    /**
     * @return bool
     */
    public function canBeImpersonated()
    {
        // For example
        return $this->customertypeid >= 10;
    }

    public function verifyUser()
    {
        return $this->hasOne('App\Models\VerifyUser', 'customer_id');
    }

    public function culture()
    {
        return $this->belongsTo(\App\Models\Culture::class, 'cultureid');
    }
}
