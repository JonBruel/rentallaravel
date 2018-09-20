<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Batchlog;

class executequeue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:executequeue {batchid?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Goes through all batchlog entries with status=1 and executes them, setting status to 2.';

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
        $batchid = null;
        if ($this->argument('batchid')) $batchid = $this->argument('batchid');
        Batchlog::executequeue($batchid);
    }
}
