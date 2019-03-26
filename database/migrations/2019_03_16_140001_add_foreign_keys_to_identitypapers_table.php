<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIdentitypapersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('identitypapers', function(Blueprint $table)
		{
            $table->foreign('contractid', 'FK_identitypapers_1')->references('id')->on('contract')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('identitypapers', function(Blueprint $table)
		{
			$table->dropForeign('FK_identitypapers_1');
		});
	}

}
