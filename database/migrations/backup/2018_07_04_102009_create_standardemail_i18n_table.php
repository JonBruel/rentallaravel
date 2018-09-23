<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStandardemailI18nTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('standardemail_i18n', function(Blueprint $table)
		{
			$table->integer('id')->default(0)->index('id');
			$table->string('culture', 5);
			$table->text('contents', 65535)->nullable();
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
		Schema::drop('standardemail_i18n');
	}

}
