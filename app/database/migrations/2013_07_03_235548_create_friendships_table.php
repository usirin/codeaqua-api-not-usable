<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFriendshipsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friendships', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('sourceId');
            $table->integer('targetId');
            $table->boolean('status')->default(false);
            $table->boolean('seen')->default(false);
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
            $table->timestamp('deletedAt')->nullable()->default(null);

            $table->index(['sourceId', 'targetId', 'status', 'seen', 'deletedAt']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('friendships');
    }

}
