<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        try {
            $this->call(UserSeeder::class);
        } catch (\Exception $e) {
            // User already exists
        }
        $this->call(WorldStoreSeeder::class);
        $this->call(WorldStoreHotspotsSeeder::class);
    }
}
