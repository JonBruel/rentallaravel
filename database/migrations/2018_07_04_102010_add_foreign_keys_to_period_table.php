<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPeriodTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('period', function(Blueprint $table)
		{
			$table->foreign('houseid', 'FK_period_1')->references('id')->on('house')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ownerid', 'FK_period_2')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('period', function(Blueprint $table)
		{
			$table->dropForeign('FK_period_1');
			$table->dropForeign('FK_period_2');
		});
	}

}
