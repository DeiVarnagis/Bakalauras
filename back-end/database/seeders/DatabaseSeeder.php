<?php

namespace Database\Seeders;

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
         \App\Models\User::factory(10)->create();
         \App\Models\DevicesLongTerm::factory(10)->create();
         \App\Models\DevicesShortTerm::factory(10)->create();
         \App\Models\Accessories::factory(10)->create();
    }
}
