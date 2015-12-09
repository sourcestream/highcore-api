<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stacks', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('environment_id')->unsigned();
            $table->foreign('environment_id')->references('id')->on('environments');
            $table->integer('template_id')->unsigned();
            $table->foreign('template_id')->references('id')->on('templates');
            $table->string('name')->unique();
            $table->json('components')->nullable();
            $table->json('parameters')->nullable();
            $table->json('stacks')->nullable();
            $table->json('ui')->nullable();
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
		Schema::drop('stacks');
	}

}
