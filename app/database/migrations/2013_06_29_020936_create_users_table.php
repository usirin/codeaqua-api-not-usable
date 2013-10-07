<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table){
			$table->increments('id');
			$table->string('username', 40);
			$table->string('password', 60);
			$table->string('email');
			$table->string('firstName', 255);
            $table->string('lastName', 255);
            $table->timestamp('birthdate');
            $table->integer('photoId')->nullable();
			$table->string('apiKey', 60);
			$table->boolean('status')->default(true);
			$table->timestamp('createdAt');
			$table->timestamp('updatedAt');
			$table->timestamp('deletedAt')->nullable()->default(null);

			$table->index(['username', 'password', 'email', 'photoId', 'deletedAt']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}