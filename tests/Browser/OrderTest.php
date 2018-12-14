<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Models\House;
use App\Models\Customertype;
use App\Models\Customerstatus;
use App\Models\Culture;
use App\Models\Currency;
use App\Models\BaseModel;
use App\Models\Contract;
use App\Models\Batchlog;
use Webklex\IMAP\Client;
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
            fwrite(STDERR, "Running bowser test, result of checking:  $value"."\n");
            self::assertTrue($value == 'true');

            // Below we click on the second vacant month, this should only trigger one type of mail
            $browser->click('@login-button')
                ->assertPathIs('/home')
                ->assertPathIs('/home')
                ->assertTitleContains('home')
                ->click("@click-order")
                ->click("@vacantmonth2")
                ->assertTitleContains("contractedit");

            $contractid = $browser->value('@id');

            $contract = Contract::Find($contractid);

            fwrite(STDERR, "Running bowser test, result of contractid:  $contractid"."\n");

            $browser->click('@next')
                ->assertTitleContains('contractupdate');

            //The contract is now committed, next to test is the E-mail worksflow.
            //Within a minute, and E-mail should arrive. We force this by running
            //addtoqueue and executequeue
            $res1 = Batchlog::addtoqueue();
            $res2 = Batchlog::executequeue();
            sleep(5); //Allow mail to be received

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
            $oFolder = $oClient->getFolder('INBOX');
            $aMessages = $oFolder->query(null)->since(Carbon::now()->subHours(1))->from('iben@hasselbalch.com')->get();

            $this->assertTrue(sizeof($aMessages) > 0);
            $findtext = "der har bestilt";
            $found = false;

            // We check for a mail from iben@hasselbalch with the subject used for order confirmation
            foreach($aMessages as $key => $message) {
                if (strpos($message->getSubject(), $findtext) !== false) $found = true;
                $aMessages[$key]->delete();
            }
            $this->assertTrue($found);

            // Next we change the order date to trigger the reminder mail to the owner



            $contract->delete();

        });
    }
}
