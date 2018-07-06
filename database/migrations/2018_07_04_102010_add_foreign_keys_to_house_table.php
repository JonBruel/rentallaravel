<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToHouseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('house', function(Blueprint $table)
		{
			$table->foreign('currencyid', 'FK_house_1')->references('id')->on('currencies')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('ownerid', 'FK_house_2')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('viewfilter', 'FK_house_3')->references('id')->on('customertype')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('house', function(Blueprint $table)
		{
			$table->dropForeign('FK_house_1');
			$table->dropForeign('FK_house_2');
			$table->dropForeign('FK_house_3');
		});
	}

}
