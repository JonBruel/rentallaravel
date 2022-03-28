<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 20-10-2018
 * Time: 17:05
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\ConfigFromDB;
//use App\Mail\DefaultMail;
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
    protected $signature = 'command:testmail {webaddress?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a test mail to jbr@consiglia.dk, according to the smail setup on the webaddress';

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
        $webaddress = 'cangeroni.hasselbalch.com';
        if ($this->argument('webaddress')) $webaddress = $this->argument('webaddress');

        $to = 'jbr3@consiglia.dk';
        $mailtext = "Dette er en prøve";
        $subject = 'Test';
        $fromname = 'Iben Hasselbalch';
        $fromaddress = 'iben@hasselbalch.com';
        $toname = 'To Jon';
        $attchmentdocs = [public_path() . '/housedocuments/1/cangeroniankomstdansk070114.doc',public_path() . '/housedocuments/1/cangeronihowtouseenglish070617.doc',public_path() . '/housedocuments/1/cangeronirutedansk070114.doc'];

        ConfigFromDB::configFromDB($webaddress);
        // With Mail::to()->send(), we cannot use to detailed Swift settings used below.

        Mail::send('email/default', ['toName' => $toname, 'fromName' => $fromname, 'contents' => $mailtext], function($message) use  ($to, $fromname, $fromaddress, $subject, $attchmentdocs) {
            $message->from($fromaddress, $fromname);
            //$message->sender(config('mail.MAIL_FROM_ADDRESS', 'rental@consiglia.dk'));
            $message->to($to);
            $message->subject($subject);
            $message->replyTo($fromaddress);
            foreach($attchmentdocs as $attchmentdoc) $message->attach($attchmentdoc);
        });

    }
}
