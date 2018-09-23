<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStandardemailI18nTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('standardemail_i18n', function(Blueprint $table)
		{
			$table->foreign('id', 'FK_standardemail_i18n_1')->references('id')->on('standardemail')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('standardemail_i18n', function(Blueprint $table)
		{
			$table->dropForeign('FK_standardemail_i18n_1');
		});
	}

}
