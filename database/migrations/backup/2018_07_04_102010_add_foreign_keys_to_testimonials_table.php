<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTestimonialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('testimonials', function(Blueprint $table)
		{
			$table->foreign('userid', 'FK_testimonials_1')->references('id')->on('customer')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('houseid', 'FK_testimonials_2')->references('id')->on('house')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('testimonials', function(Blueprint $table)
		{
			$table->dropForeign('FK_testimonials_1');
			$table->dropForeign('FK_testimonials_2');
		});
	}

}
