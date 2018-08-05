<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Customer;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     *
     * From Customer model:
     * public static $customertypes = ['Test' => 0, 'Supervisor' => 1, 'Owner' => 10, 'Administrator' => 100, 'Personel' => 110, 'Customer' => 1000];
     */
    public function boot()
    {
        $this->registerPolicies();
        foreach (Customer::$customertypes as $typename => $typevalue)
        {
            switch ($typevalue) {
                case 0:
                    Gate::define($typename, function ($user) {
                        return ($user->customertypeid <= 0);
                    });
                    break;
                case 1:
                    Gate::define($typename, function ($user) {
                        return ($user->customertypeid <= 1);
                    });
                    break;
                case 10:
                    Gate::define($typename, function ($user) {
                        return ($user->customertypeid <= 10);
                    });
                    break;
                case 100:
                    Gate::define($typename, function ($user) {
                        return ($user->customertypeid <= 100);
                    });
                    break;
                case 110:
                    Gate::define($typename, function ($user) {
                        return ($user->customertypeid <= 110);
                    });
                    break;
                case 1000:
                    Gate::define($typename, function ($user) {
                        return ($user->customertypeid <= 1000);
                    });
                    break;
            }

        }

        //
    }
}
