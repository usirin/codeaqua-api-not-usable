<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePhotosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function(Blueprint $table) {
            $table->increments('id');
            $table->text('description');
            
            $table->integer('userId');

            $table->string('original');
            $table->string('thumb')->nullable()->default(null);
            $table->string('thumb2x')->nullable()->default(null);
            $table->string('thumb4x')->nullable()->default(null);
            $table->string('square')->nullable()->default(null);
            $table->string('square2x')->nullable()->default(null);
            $table->string('square4x')->nullable()->default(null);
            $table->string('large')->nullable()->default(null);
            $table->string('large2x')->nullable()->default(null);
            $table->string('large4x')->nullable()->default(null);

            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
            $table->timestamp('deletedAt')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('photos');
    }

}
