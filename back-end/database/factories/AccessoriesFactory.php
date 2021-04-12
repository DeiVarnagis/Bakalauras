<?php

namespace Database\Factories;

use App\Models\Accessories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AccessoriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Accessories::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Antena', 'Adapteris VGA', "Adapteris HDMI", 'Adapteris Display port', "Adapteris DVI", "Adapteris Ethernal USB", 
            "Pakrovėjas Gembird 3.1A", "Pakrovėjas Whitenergy 05504", "Pakrovėjas QOLTEC 51518", 'Antena Standart UVR-AV888A', 'Antena UHF-102', "Antena DVB-T9039GU", 
            'Adapteris Mišrus(HDMI, VGA, DISPAY)',"Adapteris DVI", "Pakrovėjas QOLTEC 51518"]),
            'amount' => 1,
            'longTerm_id' => 1,
            'shortTerm_id' => null
        ];
    }
}
