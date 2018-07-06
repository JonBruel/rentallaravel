<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRightsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rights', function(Blueprint $table)
		{
			$table->foreign('customertypeid', 'FK_rights_1')->references('id')->on('customertype')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rights', function(Blueprint $table)
		{
			$table->dropForeign('FK_rights_1');
		});
	}

}
