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

class LoginTest extends DuskTestCase
{
    //use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {

        /*
        $currency= factory(Currency::class)->create([
            'id' => '2',
            'currencysymbol' => 'DKK',
            'currencyname' => 'Danske kroner',
            'code' => 208,
            'rate' => 7.45
        ]);

        $cultures = factory(Culture::class)->create([
            'id' => '1',
            'currencyid' => 2,
            'culture' => 'da_DK',
            'culturename' => 'Dansk'
        ]);

        $customerstatus = factory(Customerstatus::class)->create([
            'id' => '1',
            'status' => 'Normal'
        ]);

        $house = factory(Customertype::class)->create([
            'id' => '1',
            'customertype' => 'Supervisor'
        ]);

        $customer = factory(Customer::class)->create([
            'email' => 'taylor@laravel.com',
            'password' => '$2y$10$sCCXA9Md17kPO.dwvIOBrOemhc7P3S3rYbQKoe8nIawomjXgkEe16', // 9Bukkelo!
            'customertypeid' => 1,
            'ownerid' => 1,
            'status' => 1,
            'cultureid' => 1,
            'id' => 1
        ]);

        $house = factory(House::class)->create([
            'name' => 'Test house',
            'ownerid' => 1,
            'currencyid' => 2,
        ]);
        */

        //Using existing data
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertPathIs('/login')
                ->assertSee('E-Mail adresse')
                ->type('email', 'jbr@consiglia.dk')
                ->type('password', '9Bukkelo!');

            $value = $browser->attribute('@remme', 'checked');
            fwrite(STDERR, "Running bowser test, result of checking:  $value"."\n");
            self::assertTrue($value == 'true');

            $browser->click('@login-button')
                ->assertPathIs('/home')
                ->assertTitleContains('home');

            //Login again and check that we land on the main page
            $browser->visit('/login')
                ->assertPathIs('/home')
                ->assertTitleContains('home');

            //Logout
            $browser->visit('/logout')
                ->assertPathIs('/')
                ->assertTitle('Rental:');

            //login again
            $browser->visit('/login')
                ->assertPathIs('/login')
                ->assertSee('E-Mail adresse')
                ->type('email', 'jbr@consiglia.dk')
                ->type('password', '9Bukkelo!')
                ->click('@login-button')
                ->assertPathIs('/home')
                ->assertTitleContains('home');

        });
    }
}
