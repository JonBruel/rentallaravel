<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToContractTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contract', function(Blueprint $table)
		{
			$table->foreign('categoryid', 'FK_contract_1')->references('id')->on('categories')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('customerid', 'FK_contract_2')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('currencyid', 'FK_contract_3')->references('id')->on('currencies')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('houseid', 'FK_contract_4')->references('id')->on('house')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ownerid', 'FK_contract_5')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contract', function(Blueprint $table)
		{
			$table->dropForeign('FK_contract_1');
			$table->dropForeign('FK_contract_2');
			$table->dropForeign('FK_contract_3');
			$table->dropForeign('FK_contract_4');
			$table->dropForeign('FK_contract_5');
		});
	}

}
