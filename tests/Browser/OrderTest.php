<?php

namespace Tests\Browser;

use App\Models\BaseModel;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Models\House;
use App\Models\Customertype;
use App\Models\Customerstatus;
use App\Models\Culture;
use App\Models\Currency;
use App\Models\Accountpost;
use App\Models\Contract;
use App\Models\Batchlog;
//use Webklex\IMAP\Client;
use Webklex\IMAP\Facades\Client;
use Carbon\Carbon;

class OrderTest extends DuskTestCase
{
    //use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testOrderTestLoginOrder()
    {
         //Using existing data
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertPathIs('/login')
                ->assertSee('E-Mail adresse')
                ->type('email', 'jbr3@consiglia.dk')
                ->type('password', '9Bukkelo!');

            $value = $browser->attribute('@remme', 'checked');
            fwrite(STDERR, "Running browser test, result of checking:  $value"."\n");
            self::assertTrue($value == 'true');

            // Below we click on the second vacant month, this should only trigger one type of mail
            $browser->click('@login-button')
                ->assertPathIs('/home')
                ->assertPathIs('/home')
                ->assertTitleContains('home')
                ->click("@click-order")
                ->click("@vacantmonth4")
                ->assertTitleContains("contractedit");

            $contractid = $browser->value('@id');

            $contract = Contract::Find($contractid);

            fwrite(STDERR, "Running browser test, result of contractid:  $contractid"."\n");
            sleep(1);
            $browser->click('@next')
                ->assertTitleContains('contractupdate');

            //The contract is now committed, next to test is the E-mail worksflow.
            //Within a minute, and E-mail should arrive. We force this by running
            //addtoqueue and executequeue
            BaseModel::$ajax = true;
            $res1 = Batchlog::addtoqueue();
            BaseModel::$ajax = true;
            $res2 = Batchlog::executequeue();
            sleep(5); //Allow mail to be received

            $oClient = Client::account('default');
            $oClient->connect();
            $oFolder = $oClient->getFolder('INBOX');
            $aMessages = $oFolder->query(null)->since(Carbon::now()->subHours(1))->from('iben@hasselbalch.com')->get();

            $this->assertTrue(sizeof($aMessages) > 0);
            $emailid = 1066;
            $findtext = 'Testmail only test from new rental system: ' . $emailid;
            $found = false;

            // We check for a mail from iben@hasselbalch with the subject used for order confirmation
            foreach($aMessages as $key => $message) {
                if ($message->getSubject() == $findtext) $found = true;
                $aMessages[$key]->delete();
            }
            $this->assertTrue($found);

            // Next we change the order date to trigger the reminder mail to the owner

            $created_at = $contract->created_at->subDays(12);
            $contract->created_at = $created_at;
            $contract->save();
            $accountpost = Accountpost::where('contractid', $contractid)->where('posttypeid', 10)->first();
            $accountpost->created_at = $created_at;
            $accountpost->save();
            sleep(1);
            BaseModel::$ajax = true;
            $res1 = Batchlog::addtoqueue();
            BaseModel::$ajax = true;
            $res2 = Batchlog::executequeue();
            sleep(5); //Allow mail to be received



            $oClient->connect();
            $oFolder = $oClient->getFolder('INBOX');
            $aMessages = $oFolder->query(null)->since(Carbon::now()->subHours(1))->from('iben@hasselbalch.com')->get();

            $this->assertTrue(sizeof($aMessages) > 0);
            $emailid = 1068;
            $findtext1 = 'Testmail only test from new rental system: ' . $emailid;;
            $found1 = false;
            $emailid = 1069;
            $findtext2 = 'Testmail only test from new rental system: ' . $emailid;
            $found2 = false;

            // We check for a mail from iben@hasselbalch with the subject used for order confirmation
            foreach($aMessages as $key => $message) {
                $subject = utf8_decode(imap_utf8($message->getSubject()));
                if ($message->getSubject() == $findtext1)  $found1 = true;
                if ($message->getSubject() == $findtext2)  $found2 = true;
                $aMessages[$key]->delete();
            }
            $this->assertTrue($found1);
            $this->assertTrue($found2);
            $contract->delete();

        });
    }
}
