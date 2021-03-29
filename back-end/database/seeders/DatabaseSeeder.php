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
         \App\Models\User::factory()->create([
            'email' => 'admin@test.com',
            'admin' => true
        ]);

        \App\Models\User::factory()->create([
            'email' => 'user@test.com',
        ]);

        \App\Models\DevicesLongTerm::factory(10)->create([
            'user_id' => '1',
        ]);

        \App\Models\DevicesLongTerm::factory(10)->create([
            'user_id' => '2',
        ]);

         \App\Models\DevicesShortTerm::factory(10)->create([
            'user_id' => '1',
        ]);

        \App\Models\DevicesShortTerm::factory(10)->create([
            'user_id' => '2',
        ]);

         \App\Models\Accessories::factory(10)->create([
            'shortTerm_id' => null,
            'longTerm_id' => '1'
        ]);

        \App\Models\Accessories::factory(10)->create([
            'shortTerm_id' => '1',
            'longTerm_id' => null
        ]);
    }
}
