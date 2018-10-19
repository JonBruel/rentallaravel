<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BaseModel;
use App\Helpers\ImportFromRental;

class importfromrentalsystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:importfromrental';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from the old version of Rental';

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
        BaseModel::$ajax = true;
        ImportFromRental::import();
    }
}
