<?php

namespace Database\Factories;

use App\Models\DishCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dish>
 */
class DishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //TODO: Replace ingredients with ingredients factory

        return [
            'dish_category_id'  => DishCategory::factory()->create()->getKey(),
            'title'             => $this->faker->words(5, true),
            'video'             => $this->faker->url(),
            'tag'               => $this->faker->word(),
            'image'             => $this->faker->imageUrl(),
            'description'       => $this->faker->text(),
            'nutritional_value' => $this->faker->words(3, true),
            'exclaim'           => $this->faker->text(),
            //            'ingredients' => [],
        ];
    }
}
