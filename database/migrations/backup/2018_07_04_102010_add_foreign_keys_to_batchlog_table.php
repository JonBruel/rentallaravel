<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBatchlogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('batchlog', function(Blueprint $table)
		{
			$table->foreign('ownerid', 'FK_batchlog_1')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('posttypeid', 'FK_batchlog_2')->references('id')->on('posttypes')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('batchtaskid', 'FK_batchlog_3')->references('id')->on('batchtask')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('contractid', 'FK_batchlog_4')->references('id')->on('contract')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('accountpostid', 'FK_batchlog_5')->references('id')->on('accountposts')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('customerid', 'FK_batchlog_6')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('houseid', 'FK_batchlog_7')->references('id')->on('house')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('statusid', 'FK_batchlog_8')->references('id')->on('batchstatus')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('batchlog', function(Blueprint $table)
		{
			$table->dropForeign('FK_batchlog_1');
			$table->dropForeign('FK_batchlog_2');
			$table->dropForeign('FK_batchlog_3');
			$table->dropForeign('FK_batchlog_4');
			$table->dropForeign('FK_batchlog_5');
			$table->dropForeign('FK_batchlog_6');
			$table->dropForeign('FK_batchlog_7');
			$table->dropForeign('FK_batchlog_8');
		});
	}

}
