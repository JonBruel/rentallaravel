<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('menu', function(Blueprint $table)
		{
			$table->foreign('parentid', 'FK_menu_1')->references('id')->on('menu')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('menu', function(Blueprint $table)
		{
			$table->dropForeign('FK_menu_1');
		});
	}

}
