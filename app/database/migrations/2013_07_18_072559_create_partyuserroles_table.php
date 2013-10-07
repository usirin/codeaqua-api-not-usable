<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePartyUserRolesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partyUserRoles', function(Blueprint $table) {
            $table->increments('id');

            $table->string('name');

            $table->boolean('canCloseParty')->default(false);
            $table->boolean('canSendNotification')->default(false);
            $table->boolean('canEditPeople')->default(false);
            $table->boolean('canDeletePeople')->default(false);
            $table->boolean('canDeleteOthersPost')->default(false);

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
        Schema::drop('partyUserRoles');
    }

}
