<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //defaultHouse is reset when we impersonate, as the new user may not have access to
        //the same house as the supervisor.
        Event::listen('Lab404\Impersonate\Events\TakeImpersonation', function($event)
        {
            session(['previousDefaultHouse' => session('defaultHouse', config('app.default_house'))]);
            session(['defaultHouse' => config('app.default_house')]);
        });

        //We go back using the defaultHouse value for the impersonating user.
        Event::listen('Lab404\Impersonate\Events\LeaveImpersonation', function($event)
        {
            session(['defaultHouse' => session('previousDefaultHouse', config('app.default_house'))]);
        });
    }
}
