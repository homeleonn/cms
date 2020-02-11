<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_relationships', function (Blueprint $table) {
            $table->unsignedBigInteger('object_id');
            $table->unsignedBigInteger('term_taxonomy_id')->index();
            $table->unsignedInteger('term_order')->default(0);
        });

        DB::statement('ALTER TABLE `term_relationships` ADD PRIMARY KEY (`object_id`, `term_taxonomy_id`) USING BTREE;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_relationships');
    }
}
