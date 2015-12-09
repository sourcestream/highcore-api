<?php

use Illuminate\Database\Seeder;
use Highcore\Environment;
use Highcore\Project;

class EnvironmentsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        DB::table('environments')->delete();

        $environment = new Environment([
            'name' => 'production',
            'parameters' => [[
                    "id" => "vpc_cidr",
                    "value" => "10.0.0.0/16",
                ],
                [   "id" => "key_name",
                    "value" => "highcore-production",
                ], [
                    "id" => "private_zone",
                    "value" => "highcore-production",
                ], [
                    "id" => "cloud_key",
                    "value" => "xxxxxxxxxxxxxxxxxxxxx",
                ], [
                    "id" => "cloud_secret",
                    "sensitive" => true,
                    "value" => "xxxxxxxxxxxxxxxxxxxxxx",
            ]],
        ]);
        $environment->project()->associate(Project::wherename('highcore')->first())->save();

        $this->command->info('Environments table seeded!');
	}

}
