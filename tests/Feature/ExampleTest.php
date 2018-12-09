<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\House;
use App\Models\Posttype;

class ExampleTest extends TestCase
{
    //The trait below deletes the database
    //use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        fwrite(STDERR, "Running feature testBasicTest"."\n");
        $user = factory(User::class)->create();

        //Currencyid = 1, ownerid = 174
        $house = factory(House::class)->create();
        $name = $user->name;

        fwrite(STDERR, "User name is $name \n");

        $housename = $house->name;
        fwrite(STDERR, "House name name is $housename \n");
        $response = $this->get('/');
        $response->assertSee('Rent a house');
        $response->assertStatus(200);

        $user->delete();
        $house->delete();


    }
}
