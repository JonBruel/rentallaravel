<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contract', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('houseid')->default(0)->index('FK_contract_4');
			$table->integer('ownerid')->default(0)->index('FK_contract_5');
			$table->integer('customerid')->default(0)->index('ContractCustomerID');
			$table->integer('persons')->default(2);
			$table->text('theme', 65535)->nullable();
			$table->timestamps();
			$table->dateTime('landingdatetime')->nullable();
			$table->dateTime('departuredatetime')->nullable();
			$table->text('message', 65535)->nullable();
			$table->decimal('duration', 10, 0)->nullable()->default(7);
			$table->decimal('price', 12, 4)->nullable()->default(0.0000);
			$table->decimal('discount', 6)->nullable()->default(0.00);
			$table->decimal('finalprice', 12, 4)->nullable()->default(0.0000);
			$table->integer('currencyid')->default(0)->index('FK_contract_3');
			$table->integer('categoryid')->nullable()->default(0)->index('FK_contract_1');
			$table->string('status', 20)->nullable()->default('new');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contract');
	}

}
