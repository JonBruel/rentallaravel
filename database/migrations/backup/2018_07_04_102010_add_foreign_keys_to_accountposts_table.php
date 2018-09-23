<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAccountpostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('accountposts', function(Blueprint $table)
		{
			$table->foreign('customerid', 'FK_accountposts_1')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('contractid', 'FK_accountposts_2')->references('id')->on('contract')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('postedbyid', 'FK_accountposts_3')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('posttypeid', 'FK_accountposts_4')->references('id')->on('posttypes')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('currencyid', 'FK_accountposts_5')->references('id')->on('currencies')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('houseid', 'FK_accountposts_6')->references('id')->on('house')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ownerid', 'FK_accountposts_7')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('customercurrencyid', 'FK_accountposts_8')->references('id')->on('currencies')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('accountposts', function(Blueprint $table)
		{
			$table->dropForeign('FK_accountposts_1');
			$table->dropForeign('FK_accountposts_2');
			$table->dropForeign('FK_accountposts_3');
			$table->dropForeign('FK_accountposts_4');
			$table->dropForeign('FK_accountposts_5');
			$table->dropForeign('FK_accountposts_6');
			$table->dropForeign('FK_accountposts_7');
			$table->dropForeign('FK_accountposts_8');
		});
	}

}
