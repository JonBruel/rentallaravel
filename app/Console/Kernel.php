<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('command:updaterates')->dailyAt('12:45')->timezone('Europe/Copenhagen');
        $schedule->command('command:updaterates')->dailyAt('11:35')->timezone('Europe/Copenhagen');
        $schedule->command('command:updaterates')->dailyAt('22:45')->timezone('Europe/Copenhagen');
        $schedule->command('command:removeoldnewcontracts')->everyMinute();
        $schedule->command('command:activateawaitingaccountposts 0')->everyMinute();
        $schedule->command('command:addtoqueue')->everyMinute();
        $schedule->command('command:execute')->everyMinute();
        $schedule->command('dusk --group=guardia --env=.env')->dailyAt('08:30')->timezone('Europe/Copenhagen');
        //$schedule->command('dusk --group=guardia --env=.env')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
