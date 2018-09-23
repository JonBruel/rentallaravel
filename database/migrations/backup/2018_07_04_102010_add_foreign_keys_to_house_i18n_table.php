<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToHouseI18nTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('house_i18n', function(Blueprint $table)
		{
			$table->foreign('id', 'id')->references('id')->on('house')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('house_i18n', function(Blueprint $table)
		{
			$table->dropForeign('id');
		});
	}

}
