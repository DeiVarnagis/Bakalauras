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

        for ($i = 1; $i <= 9; $i++) {

            \App\Models\Accessories::factory(4)->create([
                'shortTerm_id' => null,
                'longTerm_id' => $i
            ]);
        }

        for ($i = 1; $i <= 9; $i++) {

            \App\Models\Accessories::factory(4)->create([
                'shortTerm_id' => $i,
                'longTerm_id' => null
            ]);
        }

        \App\Models\Inventorization::factory(1)->create();

    }
}
