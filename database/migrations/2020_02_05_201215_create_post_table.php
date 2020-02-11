<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('posts', function (Blueprint $table) {
			$table->charset = 'utf8';
			$table->collation = 'utf8_general_ci';
			$table->bigIncrements('id');
			$table->string('title');
			$table->string('slug');
			$table->string('short_title', 50)->nullable();
			$table->longText('content')->nullable();
			$table->string('post_type')->default('page');
			$table->unsignedBigInteger('parent')->nullable();
			$table->unsignedBigInteger('author')->nullable();
			$table->enum('status', ['publish', 'draft'])->default('draft');
			$table->enum('comment_status', ['open','closed'])->default('closed');
			$table->unsignedBigInteger('comment_count')->default(0);
			$table->unsignedBigInteger('visits')->default(0);
			$table->timestamps();
			$table->index('slug');
			$table->index('post_type');
			$table->index('parent');
		});
		DB::statement('ALTER TABLE `posts` ADD UNIQUE `slug_post_type_unique` (`slug`(50), `post_type`(50));');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('posts');
	}
}
