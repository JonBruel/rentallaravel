<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCulturesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cultures', function(Blueprint $table)
		{
			$table->foreign('currencyid', 'FK_cultures_1')->references('id')->on('currencies')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cultures', function(Blueprint $table)
		{
			$table->dropForeign('FK_cultures_1');
		});
	}

}
