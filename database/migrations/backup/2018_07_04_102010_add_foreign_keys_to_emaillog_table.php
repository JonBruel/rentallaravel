<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEmaillogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('emaillog', function(Blueprint $table)
		{
			$table->foreign('customerid', 'FK_emaillog_1')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('houseid', 'FK_emaillog_2')->references('id')->on('house')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ownerid', 'FK_emaillog_3')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('emaillog', function(Blueprint $table)
		{
			$table->dropForeign('FK_emaillog_1');
			$table->dropForeign('FK_emaillog_2');
			$table->dropForeign('FK_emaillog_3');
		});
	}

}
