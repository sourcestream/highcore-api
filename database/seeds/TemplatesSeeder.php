<?php

use Illuminate\Database\Seeder;
use Highcore\Template;
use Highcore\Project;

class TemplatesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        $highcore_project = Project::wherename('highcore')->first();
        DB::table('templates')->delete();

        $template = new Template([
            'name' => 'env',
            'repository'=> 'https://github.com/sourcestream/highcore-templates.git',
            'refspec' => 'master'
        ]);
        $template->project()->associate($highcore_project)->save();

        $template = new Template([
            'name' => 'highcore',
            'repository'=> 'https://github.com/sourcestream/highcore-templates.git',
            'refspec' => 'master'
        ]);
        $template->project()->associate($highcore_project)->save();

        $this->command->info('Templates table seeded!');
	}

}
