<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        fwrite(STDERR, "Running unit testExample"."\n");
        $response = $this->get('/');
        $response->assertStatus(200);
        //$this->assertTrue(true);
    }
}
