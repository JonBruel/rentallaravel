<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;

class removeoldnewcontracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:removeoldnewcontracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Romoves contract with status New if they are older than 5 minutes';

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
        Contract::removeOldNew();
    }
}
