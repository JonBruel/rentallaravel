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
use App\Models\Customer;

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

            $browser->click('@login-button')
                ->assertPathIs('/home')
                ->assertPathIs('/home')
                ->assertTitleContains('home');

            //

        });
    }
}
