<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$app = require __DIR__.'/../bootstrap/app.php';

		$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

		return $app;
	}

    public function assertArrayFiltered($arr1, $arr2, $ignore_keys = ['created_at', 'updated_at']) {
        $dot1 = array_dot($arr1);
        $dot2 = array_dot($arr2);
        $pattern = sprintf('/(.+\.)*(%s)/', join($ignore_keys, '|'));
        array_forget($arr1, preg_grep($pattern, array_keys($dot1)));
        array_forget($arr2, preg_grep($pattern, array_keys($dot2)));
        $this->assertEquals($arr1, $arr2);
    }

}
