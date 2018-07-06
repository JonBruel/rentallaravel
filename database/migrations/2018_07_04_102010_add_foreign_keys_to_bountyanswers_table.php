<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBountyanswersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bountyanswers', function(Blueprint $table)
		{
			$table->foreign('bountyid', 'FK_bountyanswers_1')->references('id')->on('bounties')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('userid', 'FK_bountyanswers_2')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bountyanswers', function(Blueprint $table)
		{
			$table->dropForeign('FK_bountyanswers_1');
			$table->dropForeign('FK_bountyanswers_2');
		});
	}

}
