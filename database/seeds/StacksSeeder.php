<?php

use Illuminate\Database\Seeder;
use Highcore\Stack;
use Highcore\Environment;
use Highcore\Template;

class StacksSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        DB::table('stacks')->delete();

        $stack = new Stack([
            'name' => 'highcore',
            'components' => [
                'api' => [
                    'id' => 'api',
                    'template_component' => 'api',
                    'parameters' => [
                        'instance_type' => [
                            'id' => 'instance_type',
                            'value' => 't2.micro'
                        ],
                        'api_db_host' => [
                            'id' => 'api_db_host',
                            'value' => env('DB_HOST')
                        ],
                        'api_db_database' => [
                            'id' => 'api_db_database',
                            'value' => env('DB_DATABASE')
                        ],
                        'api_db_username' => [
                            'id' => 'api_db_username',
                            'value' => env('DB_USERNAME')
                        ],
                        'api_db_password' => [
                            'id' => 'api_db_password',
                            'value' => Crypt::encrypt(env('DB_PASSWORD'))
                        ],
                        'api_app_key' => [
                            'id' => 'api_db_password',
                            'value' => env('APP_KEY')
                        ],
                    ],
                    'ui' => [
                        'icon' => [
                            'url' => 'images/icon.svg'
                        ],
                        'container' => [
                            'fill' => 'green'
                        ],
                        'position' => [
                            'x' => 346,
                            'y' => 210
                        ]
                    ],
                ],
                'ui' => [
                    'id' => 'ui',
                    'template_component' => 'ui',
                    'parameters' => [
                        'instance_type' => [
                            'id' => 'instance_type',
                            'value' => 't2.micro'
                        ]
                    ],
                    'components' => [
                        'vpc' => [
                            'id' => 'api',
                            'template_component' => 'api',
                        ],
                    ],
                    'ui' => [
                        'icon' => [
                            'url' => 'images/icon.svg'
                        ],
                        'container' => [
                            'fill' => 'green'
                        ],
                        'position' => [
                            'x' => 642,
                            'y' => 209
                        ]
                    ],
                ]
            ],
            'stacks' => [
                [
                    'name' => 'highcore-env'
                ]
            ]
        ]);
        $stack->environment()->associate(Environment::wherename('production')->first());
        $stack->template()->associate(Template::wherename('highcore')->first())->save();

        $stack = new Stack([
            'name' => 'highcore-env',
            'components' => [
                'vpc' => [
                    'id' => 'vpc',
                    'template_component' => 'vpc',
                    'parameters' => [
                        'cidr' => [
                            'id' => 'cidr',
                            'value' => '10.0.0.0/16'
                        ]
                    ],
                    'ui' => [
                        'icon' => [
                            'url' => 'images/icon.svg'
                        ],
                        'container' => [
                            'fill' => 'green'
                        ],
                        'position' => [
                            'x' => 642,
                            'y' => 422
                        ]
                    ],
                ],
                'subnet-a' => [
                    'id' => 'subnet-a',
                    'template_component' => 'subnet',
                    'parameters' => [
                        'cidr' => [
                            'id' => 'cidr',
                            'value' => '10.0.0.0/24'
                        ], 'az' => [
                            'id' => 'az',
                            'value' => 'eu-west-1a'
                        ]
                    ],
                    'components' => [
                        'vpc' => [
                            'id' => 'vpc',
                            'template_component' => 'vpc'
                        ]
                    ],
                    'ui' => [
                        'icon' => [
                            'url' => 'images/icon.svg'
                        ],
                        'container' => [
                            'fill' => 'green'
                        ],
                        'position' => [
                            'x' => 402,
                            'y' => 51
                        ]
                    ],
                ],
                'subnet-b' => [
                    'id' => 'subnet-b',
                    'template_component' => 'subnet',
                    'parameters' => [
                        'cidr' => [
                            'id' => 'cidr',
                            'value' => '10.0.1.0/24'
                        ], 'az' => [
                            'id' => 'az',
                            'value' => 'eu-west-1b'
                        ]
                    ],
                    'components' => [
                        'vpc' => [
                            'id' => 'vpc',
                            'template_component' => 'vpc'
                        ]
                    ],
                    'ui' => [
                        'icon' => [
                            'url' => 'images/icon.svg'
                        ],
                        'container' => [
                            'fill' => 'green'
                        ],
                        'position' => [
                            'x' => 260,
                            'y' => 58
                        ]
                    ],
                ],
                'subnet-c' => [
                    'id' => 'subnet-c',
                    'template_component' => 'subnet',
                    'parameters' => [
                        'cidr' => [
                            'id' => 'cidr',
                            'value' => '10.0.2.0/24'
                        ], 'az' => [
                            'id' => 'az',
                            'value' => 'eu-west-1c'
                        ]
                    ],
                    'components' => [
                        'vpc' => [
                            'id' => 'vpc',
                            'template_component' => 'vpc'
                        ]
                    ],
                    'ui' => [
                        'icon' => [
                            'url' => 'images/icon.svg'
                        ],
                        'container' => [
                            'fill' => 'green'
                        ],
                        'position' => [
                            'x' => 264,
                            'y' => 213
                        ]
                    ],
                ],
                'security' => [
                    'id' => 'security',
                    'template_component' => 'security',
                    'parameters' => [
                        'office_network' => [
                            'id' => 'office_network',
                            'value' => '0.0.0.0/0',
                        ],
                    ],
                    'components' => [
                        'vpc' => [
                            'id' => 'vpc',
                            'template_component' => 'vpc',
                        ],
                    ],
                    'ui' => [
                        'icon' => [
                            'url' => 'images/icon.svg'
                        ],
                        'container' => [
                            'fill' => 'green'
                        ],
                        'position' => [
                            'x' => 998,
                            'y' => 84
                        ]
                    ],
                ],
                'private_zone' => [
                    'id' => 'private_zone',
                    'template_component' => 'private_zone',
                    'parameters' => [
                        'name' => [
                            'id' => 'name',
                            'value' => 'highcore',
                        ],
                    ],
                    'components' => [
                        'vpc' => [
                            'id' => 'vpc',
                            'template_component' => 'vpc',
                        ],
                    ],
                    'ui' => [
                        'icon' => [
                            'url' => 'images/icon.svg'
                        ],
                        'container' => [
                            'fill' => 'green'
                        ],
                        'position' => [
                            'x' => 985,
                            'y' => 233
                        ]
                    ],
                ]
            ],
        ]);
        $stack->environment()->associate(Environment::wherename('production')->first());
        $stack->template()->associate(Template::wherename('env')->first())->save();

        $this->command->info('Stacks table seeded!');
	}

}
