<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBatchtaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('batchtask', function(Blueprint $table)
		{
			$table->foreign('posttypeid', 'FK_batchtask_1')->references('id')->on('posttypes')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('emailid', 'FK_batchtask_2')->references('id')->on('standardemail')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ownerid', 'FK_batchtask_3')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('houseid', 'FK_batchtask_4')->references('id')->on('house')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('batchfunctionid', 'FK_batchtask_5')->references('id')->on('batchfunctions')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('requiredposttypeid', 'FK_batchtask_6')->references('id')->on('posttypes')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('addposttypeid', 'FK_batchtask_7')->references('id')->on('posttypes')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('dontfireifposttypeid', 'FK_batchtask_8')->references('id')->on('posttypes')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('batchtask', function(Blueprint $table)
		{
			$table->dropForeign('FK_batchtask_1');
			$table->dropForeign('FK_batchtask_2');
			$table->dropForeign('FK_batchtask_3');
			$table->dropForeign('FK_batchtask_4');
			$table->dropForeign('FK_batchtask_5');
			$table->dropForeign('FK_batchtask_6');
			$table->dropForeign('FK_batchtask_7');
			$table->dropForeign('FK_batchtask_8');
		});
	}

}
