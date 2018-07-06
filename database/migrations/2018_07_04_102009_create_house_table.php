<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHouseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('house', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 50)->nullable();
			$table->string('address1', 50)->nullable();
			$table->string('address2', 50)->nullable();
			$table->string('address3', 50)->nullable();
			$table->string('country', 50)->default('Spain');
			$table->string('www', 50)->nullable()->default('http://www.everdance.com');
			$table->decimal('latitude', 20, 16)->nullable();
			$table->decimal('longitude', 20, 16)->nullable();
			$table->boolean('lockbatch')->nullable()->default(0);
			$table->integer('currencyid')->default(1)->index('FK_house_2');
			$table->integer('ownerid')->default(0)->index('ownerid2');
			$table->string('maidid', 50)->nullable();
			$table->timestamps();
			$table->integer('viewfilter')->default(1000)->index('FK_house_3');
			$table->decimal('prepayment', 10, 4)->default(0.3333);
			$table->integer('disttobeach')->default(20);
			$table->boolean('maxpersons')->default(7);
			$table->boolean('isprivate')->nullable()->default(0);
			$table->boolean('dishwasher')->nullable()->default(0);
			$table->boolean('washingmachine')->nullable()->default(0);
			$table->boolean('spa')->nullable()->default(0);
			$table->boolean('pool')->nullable()->default(0);
			$table->boolean('sauna')->nullable()->default(0);
			$table->boolean('fireplace')->nullable()->default(0);
			$table->boolean('internet')->nullable()->default(0);
			$table->boolean('pets')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('house');
	}

}
