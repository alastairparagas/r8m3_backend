<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::dropIfExists('images');
            Schema::create('images', function(Blueprint $table){
		$table->string('id', 10);
                $table->primary('id');
                $table->integer('user_id', 10)->references('id')->on('users');
                $table->string('file', 200);
                $table->decimal('rating', 15, 0);
                $table->integer('raters_count', 11);
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
            Schema::drop('images');
	}

}
