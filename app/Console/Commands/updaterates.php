<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Currencyrate;

/**
 * Class updaterates is run a a cron to update the rates of the active currencies.
 * @package App\Console\Commands
 */
class updaterates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updaterates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency rates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Currencyrate::updateCurrencies();
    }
}
