<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\DefaultMail;
use Illuminate\Support\Facades\Mail;

/**
 * Class updaterates is run a a cron to update the rates of the active currencies.
 * @package App\Console\Commands
 */
class testmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:testmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a test mail to jbr@consiglia.dk, possibly on behalf of iben.';

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
        $emailaddress = 'jbr@consiglia.dk';
        $mailtext = "Dette er en prøve";
        $from = "iben@hasselbalch.com";
        $subject = 'Test';
        $fromname = 'From Iben';
        $toname = 'To Jon';
        $attchmentdoc = [];

        Mail::to($emailaddress)
            ->send(new DefaultMail($mailtext, $subject, $from, $fromname, $toname, $attchmentdoc));
    }
}
