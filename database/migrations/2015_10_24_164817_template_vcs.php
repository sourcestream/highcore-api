<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TemplateVcs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('templates', function(Blueprint $table){
            $table->string('repository', 1024)->after('name')->default(''); //todo: after is not respected, check --pretend
            $table->string('refspec')->after('repository')->default('');
            $table->dropColumn('parameters');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('templates', function(Blueprint $table){
            $table->dropColumn('repository');
            $table->dropColumn('refspec');
            $table->json('parameters')->after('name')->nullable();
        });
	}

}
