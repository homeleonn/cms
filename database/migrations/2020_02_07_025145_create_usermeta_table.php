<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsermetaTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('usermeta', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('user_id')->index();
			$table->string('meta_key')->index();
			$table->longtext('meta_value');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('usermeta');
	}
}
