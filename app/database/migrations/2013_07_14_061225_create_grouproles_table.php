<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGrouprolesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupRoles', function(Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->boolean('canEditPage')->default(false);
            $table->boolean('canClosePage')->default(false);
            $table->boolean('canCreateParty')->default(false);
            $table->boolean('canDeleteParty')->default(false);
            $table->boolean('canDeleteWallPost')->default(false);
            $table->boolean('canDeleteWallComment')->default(false);
            $table->boolean('canSeeRequest')->default(false);
            $table->boolean('canInvitePeople')->default(false);
            $table->boolean('canEditMember')->default(false);
            $table->boolean('canDeleteMember')->default(false);

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
        Schema::drop('groupRoles');
    }

}
