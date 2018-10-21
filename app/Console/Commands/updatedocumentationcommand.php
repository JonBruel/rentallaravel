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
 * Class updatedocumentationcommand is used to send a command to phpDocumentor
 * to update the documentation fount in /public/doc.
 */
class updatedocumentationcommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updatedocumentation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activates phpDocumentor 2.9 to update documentation.';
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
        $basedir = base_path();
        $phpdocdir = $basedir.'/phpDocumentor/vendor/bin';
        $docdir = $basedir.'/public/doc';
        $appdir = $basedir.'/app';
        //php ./phpdoc -d /var/www/html/rentallaravel/app -t /var/www/html/rentallaravel/public/doc --force --template=clean
        $command = "cd $phpdocdir;php ./phpdoc -d $appdir -t $docdir --force --template=clean";
        echo $command;
        system($command);
        $command = "chown -R www-data:www-data $docdir; ";
        echo $command;
        system($command);
        $command = "chmod -R 777 $docdir; ";
        echo $command;
        system($command);

    }
}
