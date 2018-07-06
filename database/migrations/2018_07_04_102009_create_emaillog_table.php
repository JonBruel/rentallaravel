<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmaillogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emaillog', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('customerid')->default(0)->index('FK_emaillog_1');
			$table->integer('houseid')->default(0)->index('FK_emaillog_2');
			$table->integer('ownerid')->default(0)->index('FK_emaillog_3');
			$table->string('from', 100);
			$table->string('to', 100);
			$table->string('cc', 100)->nullable();
			$table->timestamps();
			$table->text('text', 65535);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('emaillog');
	}

}
