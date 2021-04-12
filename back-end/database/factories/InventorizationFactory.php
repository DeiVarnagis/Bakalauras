<?php

namespace Database\Factories;

use App\Models\Inventorization;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventorizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Inventorization::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'inventorization_time' => $this->faker->dateTimeBetween('now', '+1 week')
        ];
    }
}
