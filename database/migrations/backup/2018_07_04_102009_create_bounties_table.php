<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBountiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bounties', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('version', 5);
			$table->integer('userid')->index('FK_bounties_1');
			$table->timestamps();
			$table->text('text', 65535);
			$table->string('extra', 80);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bounties');
	}

}
