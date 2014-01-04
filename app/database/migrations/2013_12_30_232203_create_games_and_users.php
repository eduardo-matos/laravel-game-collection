<?php

use Illuminate\Database\Migrations\Migration;

class CreateGamesAndUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function ($table)
		{
			$table->increments('id');
			$table->string('email', 255);
			$table->string('password', 255);
			$table->timestamps();
		});

		Schema::create('games', function ($table)
		{
			$table->increments('id');
			$table->string('title', 128);
			$table->string('publisher', 128);
			$table->integer('owner');
			$table->boolean('completed');
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
		Schema::drop('games');
		Schema::drop('users');
	}

}