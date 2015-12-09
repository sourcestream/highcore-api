<?php

use Highcore\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        User::create(['email' => 'highcore', 'name'=> 'highcore', 'password' => '$1$YPynwn8I$fsFMZlgDEira64ahGMpUI.']);
    }
}
