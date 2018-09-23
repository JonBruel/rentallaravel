<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCulturesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cultures', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('culture', 45);
			$table->string('culturename', 45);
			$table->integer('currencyid')->default(1)->index('FK_cultures_1');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cultures');
	}

}
