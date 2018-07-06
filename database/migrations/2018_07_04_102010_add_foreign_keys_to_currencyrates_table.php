<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCurrencyratesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('currencyrates', function(Blueprint $table)
		{
			$table->foreign('currencyid', 'FK_currencyrates_1')->references('id')->on('currencies')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('currencyrates', function(Blueprint $table)
		{
			$table->dropForeign('FK_currencyrates_1');
		});
	}

}
