<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIdentitypapersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('identitypapers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('contractid')->nullable();
			$table->string('forename', 30)->nullable();
			$table->string('surname1', 30)->nullable();
			$table->string('surname2', 30)->nullable();
			$table->string('sex', 1)->default('M');
			$table->string('passportnumber', 10)->nullable()->default(0);
            $table->string('country', 21)->nullable()->default("Denmark");
            $table->dateTime('dateofissue')->nullable();
            $table->dateTime('dateofbirth')->nullable();
            $table->dateTime('arrivaldate')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('identitypapers');
	}

}
