<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStandardemailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('standardemail', function(Blueprint $table)
		{
			$table->foreign('ownerid', 'FK_standardemail_1')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('houseid', 'FK_standardemail_2')->references('id')->on('house')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('standardemail', function(Blueprint $table)
		{
			$table->dropForeign('FK_standardemail_1');
			$table->dropForeign('FK_standardemail_2');
		});
	}

}
