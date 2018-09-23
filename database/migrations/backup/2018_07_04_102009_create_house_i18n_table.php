<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHouseI18nTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('house_i18n', function(Blueprint $table)
		{
			$table->integer('id')->default(0)->index('id');
			$table->string('culture', 7)->default('da_DK');
			$table->text('description', 65535)->nullable();
			$table->text('shortdescription', 65535)->nullable();
			$table->text('veryshortdescription', 65535)->nullable();
			$table->text('route', 65535)->nullable();
			$table->text('carrental', 65535)->nullable();
			$table->text('conditions', 65535)->nullable();
			$table->text('plan', 65535)->nullable();
			$table->text('gallery', 65535)->nullable();
			$table->text('keywords', 65535)->nullable();
			$table->text('seo', 65535)->nullable();
			$table->text('nature', 65535)->nullable();
			$table->text('sports', 65535)->nullable();
			$table->text('shopping', 65535)->nullable();
			$table->text('environment', 65535)->nullable();
			$table->text('weather', 65535)->nullable();
			$table->primary(['culture','id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('house_i18n');
	}

}
