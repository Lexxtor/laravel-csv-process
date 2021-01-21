<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(3),
            'state' => 'work', //$this->faker->randomElement(['new','work','success','failure']),
            'amount' => $this->faker->randomFloat(3,0,999),
            'comment' => $this->faker->sentence(3),
        ];
    }
}
