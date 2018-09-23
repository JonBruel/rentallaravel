<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePeriodTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('period', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('year')->nullable();
			$table->integer('weeknumber')->nullable();
			$table->string('enddays', 50)->nullable();
			$table->date('from')->nullable();
			$table->date('to')->nullable();
			$table->string('theme', 50)->nullable();
			$table->integer('houseid')->default(0)->index('periodhouseid');
			$table->integer('ownerid')->default(0)->index('FK_period_2');
			$table->decimal('baseprice', 12, 4)->default(0.0000);
			$table->boolean('basepersons')->default(2)->comment('Baseprice includes the number of persons stated in the baseperson field');
			$table->boolean('maxpersons')->default(7);
			$table->decimal('personprice', 12, 4)->default(0.0000);
			$table->string('extra1', 50)->nullable()->default('0');
			$table->string('extra2', 50)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('period');
	}

}
