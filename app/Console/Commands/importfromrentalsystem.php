<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BaseModel;
use App\Helpers\ImportFromRental;

/**
 * Class importfromrentalsystem is used to import all the active data from the old Symphony 1.4 version
 * of the rental system to the new, this one, version based on Laravel.
 * @package App\Console\Commands
 */
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
