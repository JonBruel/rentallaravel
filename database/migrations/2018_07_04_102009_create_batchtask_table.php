<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBatchtaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('batchtask', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 50);
			$table->integer('posttypeid')->default(0)->index('FK_batchtask_1');
			$table->integer('emailid')->default(0)->index('FK_batchtask_2');
			$table->integer('batchfunctionid')->default(0)->index('FK_batchtask_5');
			$table->string('mailto', 50)->default('1000');
			$table->decimal('paymentbelow', 10, 4)->default(0.0000);
			$table->boolean('usepaymentbelow')->nullable();
			$table->integer('requiredposttypeid')->nullable()->default(0)->index('FK_batchtask_6');
			$table->boolean('userequiredposttypeid')->nullable();
			$table->integer('timedelaystart')->nullable()->default(0);
			$table->boolean('usetimedelaystart')->nullable();
			$table->integer('timedelayfrom')->nullable()->default(0);
			$table->boolean('usetimedelayfrom')->nullable();
			$table->integer('addposttypeid')->nullable()->default(0)->index('FK_batchtask_7');
			$table->boolean('useaddposttypeid')->nullable();
			$table->integer('dontfireifposttypeid')->default(0)->index('FK_batchtask_8');
			$table->boolean('usedontfireifposttypeid')->nullable();
			$table->integer('ownerid')->default(0)->index('FK_batchtask_3');
			$table->integer('houseid')->default(0)->index('FK_batchtask_4');
			$table->dateTime('activefrom')->nullable()->default('2000-01-01 00:00:00');
			$table->boolean('active')->default(1);
			$table->index(['name','houseid'], 'name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('batchtask');
	}

}
