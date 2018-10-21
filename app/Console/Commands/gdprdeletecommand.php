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
use App\Helpers\GDPRDelete;

/**
 * Class gdprdeletecommand is used to remove old customers from the database.
 * @package App\Console\Commands
 */
class gdprdeletecommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:gdprdelete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes or annonymises customers with no recent activity.';

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
        GDPRDelete::gdprdelete();
    }
}
