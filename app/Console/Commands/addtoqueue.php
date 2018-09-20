<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Batchlog;

class addtoqueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addtoqueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the queue before executing the queue';

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
        App/Models/BaseModel::$ajax = true;
        Batchlog::addtoqueue();
    }
}
