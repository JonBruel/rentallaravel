<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRightsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rights', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('script', 20)->default('/index.php');
			$table->string('path', 80)->default('/home');
			$table->integer('customertypeid')->default(1000)->index('customertypeid2');
			$table->string('rights')->default('Show');
			$table->unique(['script','path','customertypeid'], 'combinations');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rights');
	}

}
