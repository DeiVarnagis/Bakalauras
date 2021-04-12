<?php

namespace Database\Factories;

use App\Models\DevicesLongTerm;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class DevicesLongTermFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DevicesLongTerm::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {


        return [
            'code' => $this->faker->randomElement(['PK145632', 'TK4569879', 'RT4586644', 'PK8998656', 'MK5698446', 'PK48569874', 'TN458965132', 'KA45896235']).Str::random(3),
            'name' => $this->faker->randomElement(['Maršrutizatorius Tenda AC6','Maršrutizatorius Xiaomi Mi Wi-Fi Router 4',  'Maršrutizatorius Asus RT-AC86U',
            'Nešiojamas kompiuteris Lenovo', 'Adapteris', 'Plančetinis kompiuteris', 'Telefonas', 'Vaizdo kamera', 'Nešiojamas kompiuteris Dell', 'Nešiojamas kompiuteris HP']),
            'serialNumber' =>  $this->faker->randomElement(['LT145632', 'LT4569879', 'LT4586644', 'LT8998656', 'LT5698446', 'LT48569874', 'LT458965132', 'LT45896235']).Str::random(3),
            'amount' => 1,
            'user_id' => 2,
            'src' => null
        ];
    }
}
