<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Models\Accountpost;
use App\Models\Period;
use App\Models\Contractline;
use App\Models\Contract;
use App\Models\Batchlog;
use App\Models\Batchtask;
use Tests\Mail\CheckMail;
use Carbon\Carbon;

class OrderTest extends DuskTestCase
{
    //use DatabaseMigrations;

    /**
     * Test of first steps in order system: Order, get confirmation mail, get payment reminder to customer and owner, no payment
     * and get cancellation mail, contract uncommitted.
     *
     * @return void
     */
    public function testOrderTestLoginOrder()
    {
        //Using existing data
        $this->browse(function (Browser $browser) {
            $browser->visit('/logout');
            sleep(3);
            $browser->visit('/login')
                ->assertPathIs('/login')
                ->assertSee('E-Mail adresse')
                ->type('email', 'jbr3@consiglia.dk')
                ->type('password', '9Bukkelo!');

            $value = $browser->attribute('@remme', 'checked');
            fwrite(STDERR, "Running browser test, result of checking:  $value" . "\n");
            self::assertTrue($value == 'true');

            // Below we click on the second vacant month, this should only trigger one type of mail
            $browser->click('@login-button')
                ->assertPathIs('/home')
                ->assertPathIs('/home')
                ->assertTitleContains('home')
                ->click("@click-order")
                ->waitFor("@vacantmonth4")
                ->click("@vacantmonth4")
                ->assertTitleContains("contractedit");

            $contractid = $browser->value('@id');

            $contract = Contract::Find($contractid);

            fwrite(STDERR, "Running browser test, result of contractid:  $contractid" . "\n");
            $browser->waitFor('@next');
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
            $this->assertTrue($checkMail->checkMail([1070]));

            echo("Order - mails - no payment - no payment mail, checked");

            $contract = Contract::Find($contractid);
            $this->assertTrue(($contract->status == "Uncommitted"));

            //Check that orderlines are deleted
            $this->assertTrue(Contractline::where('contractid', $contractid)->count() == 0);
            $contract->delete();
            $checkMail->deleteAll();
        });

    }

    /**
     * @group paid
     * Test of second steps in order system: Order, get confirmation mail, get payment reminder to customer and owner, payment
     * payment receipt, get final payment reminder, pay, get welcome
     *
     * @return void
     */
    public function testOrderAndPayment()
    {
        //Using existing data
        $this->browse(function (Browser $browser) {
            $browser->visit('/logout');
            sleep(3);
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
                ->waitFor("@vacantmonth5")
                ->click("@vacantmonth5")
                ->assertTitleContains("contractedit");

            $contractid = $browser->value('@id');

            $contract = Contract::Find($contractid);

            fwrite(STDERR, "Running browser test, result of contractid:  $contractid"."\n");
            $browser->waitFor('@next');
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
            $this->assertTrue($checkMail->checkMail([1069]));

            // Next we pay, using the browser to pay.
            $browser->visit('/contract/listaccountposts/' . $contractid)
                ->select('@selectLanguage', 'en_GB');

            $amount = $browser->value('@amount');
            $amount = str_replace(',', '', $amount);
            $amount = round($amount/3) + 1;
            $browser->type('@amount', $amount);
            $browser->click('@next');

            // We gain 300 seconds here, at the addtoqueue gives the administrator 300 seconds leeway to fix things
            $accountpost = Accountpost::where('contractid', $contractid)->where('posttypeid', 50)->first();
            $accountpost->created_at = $created_at->subSeconds(301);
            $accountpost->save();

            Batchlog::addtoqueue();
            Batchlog::executequeue();
            $this->assertTrue($checkMail->checkMail([1071]));

            // We want to check if reminder mail about arrival time is sent out a the predefines time before start. We need
            // to fiddle with the from field of the contract

            $periodid = Contractline::where('contractid', $contractid)->first()->periodid;
            $period = Period::Find($periodid);
            $from = $period->from;
            $period->from = Carbon::now()->addDays(89);
            $period->save();

            // Ensure that we send mail regarding arrival time, it is not used anymore...
            $batchtask = Batchtask::Find(1216);
            $batchtask->mailto = 1000;
            $batchtask->save();
            $batchtask = Batchtask::Find(1217);
            $batchtask->mailto = 1000;
            $batchtask->save();

            Batchlog::addtoqueue();
            Batchlog::executequeue();
            $period->from = $from;
            $period->save();
            $this->assertTrue($checkMail->checkMail([1072]));


            // Reminder final payment
            $periodid = Contractline::where('contractid', $contractid)->first()->periodid;
            $period = Period::Find($periodid);
            $from = $period->from;
            $period->from = Carbon::now()->addDays(34);
            $period->save();
            Batchlog::addtoqueue();
            Batchlog::executequeue();
            $period->from = $from;
            $period->save();
            $this->assertTrue($checkMail->checkMail([1075]));

            // Register final payment
            $browser->visit('/contract/listaccountposts/' . $contractid)
                ->select('@selectLanguage', 'en_GB');

            $amount = $browser->value('@amount');
            $amount = str_replace(',', '', $amount);
            $browser->select('@posttypeid', 100)
                    ->type('@amount', $amount)
                    ->click('@next');

            // We gain 300 seconds here, at the addtoqueue gives the administrator 300 seconds leeway to fix things
            $accountpost = Accountpost::where('contractid', $contractid)->where('posttypeid', 100)->first();
            $accountpost->created_at = $created_at->subSeconds(301);
            $accountpost->save();
            Batchlog::addtoqueue();
            Batchlog::executequeue();
            $this->assertTrue($checkMail->checkMail([1076]));

            // Second reminder of arrival time
            $period->from = Carbon::now()->addDays(14);
            $period->save();
            Batchlog::addtoqueue();
            Batchlog::executequeue();
            $period->from = $from;
            $period->save();
            $contract->delete();

            $this->assertTrue($checkMail->checkMail([1073]));
            $checkMail->deleteAll();
        });
    }



    /**
     * @group earlypaid
     * Test of second steps in order system: Order, get confirmation mail, get payment reminder to customer and owner, payment
     * payment receipt, ...
     *
     * @return void
     */
    public function testOrderAndEarlyPayment()
    {
        //Using existing data
        $this->browse(function (Browser $browser) {
            $browser->visit('/logout');
            sleep(3);
            $browser->visit('/login')
                ->assertPathIs('/login')
                ->assertSee('E-Mail adresse')
                ->type('email', 'jbr3@consiglia.dk')
                ->type('password', '9Bukkelo!');

            $value = $browser->attribute('@remme', 'checked');
            fwrite(STDERR, "Running browser test, result of checking:  $value" . "\n");
            self::assertTrue($value == 'true');

            // Below we click on the second vacant month, this should only trigger one type of mail
            $browser->click('@login-button')
                ->assertPathIs('/home')
                ->assertPathIs('/home')
                ->assertTitleContains('home')
                ->click("@click-order")
                ->waitFor("@vacantmonth5")
                ->click("@vacantmonth5")
                ->assertTitleContains("contractedit");

            $contractid = $browser->value('@id');

            $contract = Contract::Find($contractid);

            fwrite(STDERR, "Running browser test, result of contractid:  $contractid" . "\n");
            $browser->waitFor('@next');
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
            $this->assertTrue($checkMail->checkMail([1069]));

            // Next we pay, using the browser to pay.
            $browser->visit('/contract/listaccountposts/' . $contractid)
                ->select('@selectLanguage', 'en_GB');

            $amount = $browser->value('@amount');
            $amount = str_replace(',', '', $amount);
            $browser->select('@posttypeid', 100)
                ->type('@amount', $amount)
                ->click('@next');

            // We gain 300 seconds here, at the addtoqueue gives the administrator 300 seconds leeway to fix things
            $accountpost = Accountpost::where('contractid', $contractid)->where('posttypeid', 100)->first();
            $accountpost->created_at = $created_at->subSeconds(301);
            $accountpost->save();

            // We push time until 24 days before rental start
            $periodid = Contractline::where('contractid', $contractid)->first()->periodid;
            $period = Period::Find($periodid);
            $from = $period->from;
            $period->from = Carbon::now()->addDays(24);
            $period->save();

            Batchlog::addtoqueue();
            Batchlog::executequeue();
            $period->from = $from;
            $period->save();
            $contract->delete();
            $this->assertTrue($checkMail->checkMail([1177]));

            $checkMail->deleteAll();
        });
    }

}
