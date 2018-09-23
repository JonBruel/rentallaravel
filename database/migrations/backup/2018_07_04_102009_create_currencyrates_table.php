<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCurrencyratesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('currencyrates', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('currencyid')->default(1)->index('FK_currencyrates_1');
			$table->dateTime('created_at');
			$table->decimal('rate', 20, 8)->default(1.00000000);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('currencyrates');
	}

}
