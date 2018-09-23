<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBountiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bounties', function(Blueprint $table)
		{
			$table->foreign('userid', 'FK_bounties_1')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bounties', function(Blueprint $table)
		{
			$table->dropForeign('FK_bounties_1');
		});
	}

}
