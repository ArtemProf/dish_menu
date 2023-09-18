<?php

namespace Database\Seeders;

use App\Models\DishIngredientCategory;

class DishIngredientCategorySeeder extends BaseSeeder
{
    const JSON_PATH = 'database/seeders/data/dish_ingredient_categories_seeds.json';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = $this->getSeedsFromJsonOrFail(self::JSON_PATH);

        foreach ($seeds as $seed) {
            unset($seed['id']);
            (new DishIngredientCategory($seed))->save();
        }
    }
}
