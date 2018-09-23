<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePosttypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('posttypes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('posttype', 45);
			$table->string('comment', 45)->nullable();
			$table->integer('defaultamount')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('posttypes');
	}

}
