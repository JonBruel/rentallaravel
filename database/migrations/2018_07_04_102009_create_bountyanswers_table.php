<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBountyanswersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bountyanswers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('bountyid')->index('FK_bountyanswers_1');
			$table->integer('userid')->index('FK_bountyanswers_2');
			$table->timestamps();
			$table->text('text', 65535);
			$table->string('version', 5);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bountyanswers');
	}

}
