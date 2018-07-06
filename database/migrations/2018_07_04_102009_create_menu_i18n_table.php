<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenuI18nTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menu_i18n', function(Blueprint $table)
		{
			$table->integer('id')->index('Index_2');
			$table->string('culture', 5)->default('da_DK');
			$table->string('text', 25);
			$table->primary(['id','culture']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('menu_i18n');
	}

}
