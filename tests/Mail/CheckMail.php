<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 16-12-2018
 * Time: 16:50
 */

namespace Tests\Mail;

use Webklex\IMAP\Facades\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Exceptions\GetMessagesFailedException;

class CheckMail
{
    private $client = null;
    private $inbox = null;
    private $messages = null;

    public function __construct() {
        $this->client = Client::account('default');
    }

    public function deleteAll() {
        $messages = $this->getInbox();
        foreach ($messages as $key => $message) $messages[$key]->delete();

    }

    private function getInbox() {
        try {
            $this->client->connect();
            $this->inbox = $this->client->getFolder('INBOX');
            $this->messages = $this->inbox->query(null)->since(Carbon::now()->subHours(1))->from('Iben Hasselbalch')->get();
        }
        catch (GetMessagesFailedException $e)
        {
            Log::error('Error in imap handler, getting inbox: ' . $e->getMessage());
        }
        return $this->messages;
    }

    public function checkMail($mailids, $tries = 100)
    {
        if (!is_array($mailids)) $mailids = [$mailids];
        while($tries > 0) {
            $tries--;
            $retval = [];
            foreach ($mailids as $mailid) $retval[$mailid] = false;
            $messages = $this->getInbox();
            foreach ($messages as $key => $message) {
                foreach ($mailids as $mailid) {
                    $findtext = 'Testmail only test from new rental system: ' . $mailid;
                    if ($message->getSubject() == $findtext) {
                        $retval[$mailid] = true;
                        $messages[$key]->delete();
                    }
                }
            }
            // We only return true if all found
            $allretval = true;
            foreach  ($mailids as $mailid) $allretval = $retval[$mailid] && $allretval;
            if ($allretval) return true;
            sleep(1);
        }

        return $allretval;
    }
}