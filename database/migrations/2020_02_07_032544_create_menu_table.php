<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menu', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('menu_id')->index();
			$table->unsignedTinyInteger('object_id')->index();
			$table->string('name');
			$table->string('origname');
			$table->string('url');
			$table->string('type', 20);
			$table->unsignedTinyInteger('parent');
			$table->tinyInteger('sort');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('menu');
	}
}
