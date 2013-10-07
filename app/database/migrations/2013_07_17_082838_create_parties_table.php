<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePartiesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parties', function(Blueprint $table) {
            $table->increments('id');
            
            $table->string('hosterType');
            $table->integer('hosterId');
            $table->integer('typeId');
            $table->integer('locationId');
            $table->integer('photoId');

            $table->timestamp('startTime');
            $table->timestamp('endTime');

            $table->string('name');

            $table->boolean('isPrivate')->default(false);
            $table->boolean('isAlcohol')->default(false);
            $table->boolean('isSmoking')->default(false);
            $table->boolean('isDressCode')->default(false);
            $table->boolean('isMusic')->default(false);
            $table->boolean('isFood')->default(false);

            $table->boolean('canAttendersInvite')->default(false);

            $table->decimal('price', 6, 2)->nullable()->default(null);

            $table->text('extraInfo')->nullable()->default(null);

            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
            $table->timestamp('deletedAt')->nullable()->default(null);

            // $table->index(['hosterType', 'hosterId', 'typeId', 'startTime', 'endTime', 'deletedAt', 'createdAt']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('parties');
    }

}
