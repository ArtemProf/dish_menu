<?php

namespace Database\Seeders;

use App\Models\DishIngredient;

class DishIngredientSeeder extends BaseSeeder
{
    const JSON_PATH = 'database/seeders/data/dish_ingredients_seeds.json';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = $this->getSeedsFromJsonOrFail(self::JSON_PATH);

        foreach ($seeds as $seed) {
            unset($seed['id']);
            //$seed['dish_id']++;
            (new DishIngredient($seed))->save();
        }
    }
}
