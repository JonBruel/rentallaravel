<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Accountpost;

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
