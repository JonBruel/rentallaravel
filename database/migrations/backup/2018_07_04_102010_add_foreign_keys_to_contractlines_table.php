<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToContractlinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contractlines', function(Blueprint $table)
		{
			$table->foreign('contractid', 'FK_contractlines_1')->references('id')->on('contract')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('periodid', 'FK_contractlines_2')->references('id')->on('period')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contractlines', function(Blueprint $table)
		{
			$table->dropForeign('FK_contractlines_1');
			$table->dropForeign('FK_contractlines_2');
		});
	}

}
