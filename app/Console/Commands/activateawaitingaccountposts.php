<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Accountpost;

/**
 * Class activateawaitingaccountposts has the function to activate
 * temporary account post after a while where they have not been changed.
 * @package App\Console\Commands
 */
class activateawaitingaccountposts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:activateawaitingaccountposts {timeout?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Changes accountsposts with posttypeid 140 to 110 after timeout (default 900) seconds';

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
        $timeout = 900;
        if ($this->argument('timeout')) $timeout = $this->argument('timeout');
        Accountpost::activateAwaitingAccountposts($timeout);
    }
}
