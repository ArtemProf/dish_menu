<?php

namespace Database\Seeders;


use App\Models\DishCategory;

class DishCategorySeeder extends BaseSeeder
{
    const JSON_PATH = 'database/seeders/data/dish_categories_seeds.json';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = $this->getSeedsFromJsonOrFail(self::JSON_PATH);

        foreach ($seeds as $seed) {
            unset($seed['id']);
            (new DishCategory($seed))->save();
        }
    }
}
