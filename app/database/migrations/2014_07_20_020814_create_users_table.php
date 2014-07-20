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
            Schema::create('users', function(Blueprint $table)
            {
		$table->increments('id', 10)->unique();
                $table->string('username', 16)->unique();
                $table->string('password', 60);
                $table->string('email', 30)->unique();
                $table->integer('uploaded_count', 11);
                $table->string('remember_token', 50);
		$table->timestamps();
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
