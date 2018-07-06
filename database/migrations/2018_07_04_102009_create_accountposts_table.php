<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountpostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accountposts', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('houseid')->default(0)->index('FK_accountposts_6');
			$table->integer('ownerid')->default(0)->index('FK_accountposts_7');
			$table->integer('customerid')->default(0)->index('FK_accountposts_1');
			$table->string('postsource', 45);
			$table->decimal('amount', 12, 4);
			$table->integer('currencyid')->default(0)->index('FK_accountposts_5');
			$table->integer('customercurrencyid')->default(0)->index('FK_accountposts_8');
			$table->decimal('usedrate', 20, 8)->default(0.00000000);
			$table->string('text', 45);
			$table->timestamps();
			$table->integer('contractid')->default(0)->index('FK_accountposts_2');
			$table->integer('posttypeid')->default(0)->index('FK_accountposts_4');
			$table->integer('postedbyid')->default(0)->index('FK_accountposts_3');
			$table->integer('passifiedby')->default(0);
			$table->dateTime('returndate')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('accountposts');
	}

}
