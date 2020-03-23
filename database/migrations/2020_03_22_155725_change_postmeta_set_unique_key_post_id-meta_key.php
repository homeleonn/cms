<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePostmetaSetUniqueKeyPostIdMetaKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `blog`.`postmeta` ADD UNIQUE (`post_id`, `meta_key`(20))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		
    }
}
