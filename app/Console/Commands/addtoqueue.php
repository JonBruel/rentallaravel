<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Batchlog;
use App\Models\BaseModel;

/**
 * Class addtoqueue creates a batchtask queue, which can be executed by executequeue. Should
 * be run regularily. This is a part of the workflow system.
 * @package App\Console\Commands
 */
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
        BaseModel::$ajax = true;
        Batchlog::addtoqueue();
    }
}
