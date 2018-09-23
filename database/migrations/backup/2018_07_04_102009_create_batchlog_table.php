<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBatchlogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('batchlog', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('statusid')->default(0)->index('FK_batchlogk_1');
			$table->integer('posttypeid')->default(0)->index('FK_batchlogbak_2');
			$table->integer('batchtaskid')->default(0);
			$table->integer('contractid')->default(0)->index('FK_batchlogbak_4');
			$table->integer('accountpostid')->default(0)->index('FK_batchlogbak_5');
			$table->integer('emailid')->nullable()->default(0);
			$table->integer('customerid')->default(0)->index('FK_batchlogbak_6');
			$table->integer('houseid')->default(0)->index('FK_batchlogbak_7');
			$table->integer('ownerid')->default(0)->index('FK_batchlogbak_8');
			$table->timestamps();
			$table->unique(['batchtaskid','accountpostid'], 'contractid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('batchlog');
	}

}
