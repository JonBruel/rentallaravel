<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCustomerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customer', function(Blueprint $table)
		{
			$table->foreign('customertypeid', 'FK_customer_1')->references('id')->on('customertype')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('ownerid', 'FK_customer_2')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('cultureid', 'FK_customer_3')->references('id')->on('cultures')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('status', 'FK_customer_4')->references('id')->on('customerstatus')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customer', function(Blueprint $table)
		{
			$table->dropForeign('FK_customer_1');
			$table->dropForeign('FK_customer_2');
			$table->dropForeign('FK_customer_3');
			$table->dropForeign('FK_customer_4');
		});
	}

}
