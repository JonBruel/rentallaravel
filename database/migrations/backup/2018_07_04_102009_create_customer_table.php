<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 50)->nullable();
			$table->string('address1', 50)->nullable();
			$table->string('address2', 50)->nullable();
			$table->string('address3', 50)->nullable();
			$table->string('country', 50)->nullable();
			$table->string('lasturl', 50)->nullable()->default('http://www.everdance.com')->comment('used for sending appropiate login information in an environment with various urls.');
			$table->string('telephone', 20)->nullable();
			$table->string('mobile', 20)->nullable();
			$table->string('email', 50)->nullable();
			$table->string('login', 50)->nullable()->unique('customerlogin');
			$table->string('password', 191)->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->timestamps();
			$table->text('notes')->nullable();
			$table->integer('customertypeid')->nullable()->default(1000)->index('customertypeid');
			$table->integer('ownerid')->default(0)->index('ownerid');
			$table->integer('houselicenses')->nullable()->default(1);
			$table->integer('status')->default(1)->index('FK_customer_4');
			$table->integer('cultureid')->default(1)->index('FK_customer_3');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customer');
	}

}
