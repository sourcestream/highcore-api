<?php

use Illuminate\Database\Seeder;
use Highcore\Project;

class ProjectsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        DB::table('projects')->delete();

        $project = Project::create([
            'name' => 'highcore',
        ]);

        $this->command->info('Projects table seeded!');
	}

}
