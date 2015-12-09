<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

        $this->call('UsersSeeder');

        DB::table('stacks')->delete(); //references Projects
        DB::table('environments')->delete(); //references Projects
        DB::table('templates')->delete(); //references Projects

        $this->call('ProjectsSeeder');

        $this->call('TemplatesSeeder');
        $this->call('EnvironmentsSeeder');
        $this->call('StacksSeeder');
	}

}
