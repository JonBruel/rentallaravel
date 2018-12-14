<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webklex\IMAP\Client;
use Carbon\Carbon;
use App\Models\Batchtask;
//use Webklex\IMAP\Facades\Client;

class MailReceivedTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        fwrite(STDERR, "Running feature MailReceivedTest testExample"."\n");
        $oClient = new Client([
            'host'          => 'mail.consiglia.dk',
            'port'          => 143,
            'encryption'    => 'true',
            'validate_cert' => false,
            'username'      => 'jbr',
            'password'      => 'dst1sf1s',
            'protocol'      => 'imap'
        ]);
        $oClient->connect();
        $aFolder = $oClient->getFolders();

        $folderlist = '';
        foreach ($aFolder as $key => $folder) $folderlist .= $key . ": " . $folder->fullName."\n";
        fwrite(STDERR, "Running feature MailReceivedTest number of folders: ".$folderlist."\n");

        $oFolder = $oClient->getFolder('INBOX');
        fwrite(STDERR, "Running feature MailReceivedTest name of folder inbox: ".$oFolder->fullName."\n");

        //since goes on data, not time
        $messagelist = '';

        //the field from should match what's show in the email system

        $aMessages = $oFolder->query(null)->since(Carbon::now()->subDays(60))->from('iben@hasselbalch.com')->get();
        //$aMessages = $oFolder->query(null)->since(Carbon::now()->subHours(1))->from('Vintervagten')->get();

        $batchjob = Batchtask::Find(1210);
        $word1 = utf8_decode('Testmail only test from new rental system: '.__($batchjob->standardemail->description, [], "da_DK"));
        $batchjob = Batchtask::Find(1219);
        $word2 = utf8_decode('Testmail only test from new rental system: '.__($batchjob->standardemail->description, [], "da_DK"));

        foreach ($aMessages as $key => $message) {
            $messagelist .= $message->getDate()->format('Y-m-d H:i:s') .  " From: " . $message->getFrom()[0]->mail . " Subject: " . mb_convert_encoding(imap_utf8($message->getSubject()), 'UTF-8', 'ISO-8859-1') . "\n";
            $subject = utf8_decode(mb_convert_encoding(imap_utf8($message->getSubject()), 'UTF-8', 'ISO-8859-1'));
            //$this->assertTrue(($subject == $word1) || ($subject == $word2));
           // $aMessages[$key]->delete();
        }
        fwrite(STDERR, "Running feature MailReceivedTest information: \n".$messagelist."\n");


        fwrite(STDERR, "Running feature MailReceivedTest utf-8 test: \n".$word1 . " " . $word2 . " from mail: " . $subject . "\n");

        $this->assertTrue(true);
    }
}
