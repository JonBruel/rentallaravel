<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractlinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contractlines', function(Blueprint $table)
		{
			$table->integer('periodid')->default(0)->index('periodid');
			$table->integer('contractid')->default(0)->index('contractid');
			$table->decimal('quantity', 6)->default(1.00);
			$table->increments('id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contractlines');
	}

}
