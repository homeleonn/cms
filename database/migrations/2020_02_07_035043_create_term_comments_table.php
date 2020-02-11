<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('comment_id');
            $table->unsignedBigInteger('comment_post_id')->index();
            $table->unsignedBigInteger('comment_author_id');
            $table->string('comment_author');
            $table->string('comment_author_email')->index();
            $table->string('comment_author_url');
            $table->string('comment_author_ip');
            $table->string('comment_author_agent');
            $table->text('comment_content');
            $table->integer('comment_karma');
            $table->boolean('comment_confirmed');
            $table->unsignedBigInteger('comment_parent')->index();
            $table->timestamp('comment_created_at')->useCurrent();
            $table->timestamp('comment_updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
