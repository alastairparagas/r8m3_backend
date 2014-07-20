<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::dropIfExists('rates');
            Schema::create('rates', function(Blueprint $table){
		$table->increments('id')->unique();
                $table->integer('user_id', 10)->references('id')->on('users');
                $table->string('image_id', 10)->references('id')->on('images');
                $table->integer('score', 2);
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
            Schema::drop('rates');
	}

}
