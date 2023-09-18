<?php

namespace Database\Factories;

use App\Models\CookList;
use App\Models\Dish;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CookListItem>
 */
class CookListItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cook_list_id' => CookList::factory()->create(),
            'dish_id'      => Dish::factory()->create(),
            'user_id'      => User::factory()->create(),
            'amount'       => $this->faker->numberBetween(1, 10),
        ];
    }
}
