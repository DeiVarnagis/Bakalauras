<?php

namespace Database\Factories;

use App\Models\DevicesShortTerm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DevicesShortTermFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DevicesShortTerm::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => Str::random(5),
            'name' => Str::random(5),
            'serialNumber' => Str::random(10),
            'amount' => 1,
            'user_id' => 1,
            'src' => null
        ];
    }
}
