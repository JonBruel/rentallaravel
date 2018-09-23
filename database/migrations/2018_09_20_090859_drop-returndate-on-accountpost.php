<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropReturndateOnAccountpost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accountposts', function (Blueprint $table) {
            $table->dropColumn(['returndate']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accountposts', function (Blueprint $table) {
            $table->dateTime('returndate')->nullable();
        });
    }
}
