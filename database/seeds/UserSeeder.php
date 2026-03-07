<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(['name' => 'Admin', 'password' => Hash::make('admin123'), 'email' => 'test@few.io', 'is_admin' => true]);
    }
}
