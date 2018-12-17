<?php

namespace Tests\Browser;

use App\Models\BaseModel;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Models\Accountpost;
use App\Models\Contractline;
use App\Models\Contract;
use App\Models\Batchlog;
use Tests\Mail\CheckMail;

class OrderTest extends DuskTestCase
{
    //use DatabaseMigrations;

    /**
     * Test of first steps in order system: Order, get confirmation mail, get payment reminder to customer and owner, no payment
     * and ge cancellation mail, contract uncommitted.
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

            $checkMail = new CheckMail();
            $checkMail->deleteAll();

            //The contract is now committed, next to test is the E-mail worksflow.
            //Within a minute, and E-mail should arrive. We force this by running
            //addtoqueue and executequeue
            //BaseModel::$ajax = true;
            Batchlog::addtoqueue();
            Batchlog::executequeue();
            sleep(1); //Allow mail to be received

            $this->assertTrue($checkMail->checkMail(1066));

            // Next we change the order date to trigger the reminder mail to the owner
            $created_at = $contract->created_at->subDays(5);
            $contract->created_at = $created_at;
            $contract->save();
            $accountpost = Accountpost::where('contractid', $contractid)->where('posttypeid', 10)->first();
            $accountpost->created_at = $created_at;
            $accountpost->save();

            Batchlog::addtoqueue();
            Batchlog::executequeue();
            sleep(1); //Allow mail to be received

            $this->assertTrue($checkMail->checkMail([1068]));


            // Next we change the order date to trigger the reminder mail to the customer, total 11 days after order
            $created_at = $contract->created_at->subDays(6);
            $contract->created_at = $created_at;
            $contract->save();
            $accountpost = Accountpost::where('contractid', $contractid)->where('posttypeid', 10)->first();
            $accountpost->created_at = $created_at;
            $accountpost->save();

            Batchlog::addtoqueue();
            Batchlog::executequeue();
            sleep(1); //Allow mail to be received

            $this->assertTrue($checkMail->checkMail([1069]));


            // Next we change the order date to trigger the letter to customer about cancelled order, total 21 days after order
            $created_at = $contract->created_at->subDays(10);
            $contract->created_at = $created_at;
            $contract->save();
            $accountpost = Accountpost::where('contractid', $contractid)->where('posttypeid', 10)->first();
            $accountpost->created_at = $created_at;
            $accountpost->save();

            Batchlog::addtoqueue();
            Batchlog::executequeue();
            sleep(1); //Allow mail to be received

            $this->assertTrue($checkMail->checkMail([1070]));

            echo("Order - mails - no payment - no payment mail, checked");

            $contract = Contract::Find($contractid);
            $this->assertTrue(($contract->status == "Uncommitted"));

            //Check that orderlines are deleted
            $this->assertTrue(Contractline::where('contractid', $contractid)->count() == 0);

        });
    }
}
