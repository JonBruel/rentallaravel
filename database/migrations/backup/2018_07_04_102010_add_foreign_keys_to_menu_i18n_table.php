<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMenuI18nTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('menu_i18n', function(Blueprint $table)
		{
			$table->foreign('id', 'FK_menu_i18n_1')->references('id')->on('menu')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('menu_i18n', function(Blueprint $table)
		{
			$table->dropForeign('FK_menu_i18n_1');
		});
	}

}
