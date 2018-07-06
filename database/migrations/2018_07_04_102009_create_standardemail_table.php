<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStandardemailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('standardemail', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('description', 50)->default('Describe me');
			$table->integer('ownerid')->index('FK_standardemail_1');
			$table->integer('houseid')->index('FK_standardemail_2');
			$table->string('extra', 50)->nullable();
			$table->timestamps();
			$table->unique(['description','houseid'], 'description');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('standardemail');
	}

}
