<?php

namespace Database\Seeders;

use App\Models\Dish;

class DishSeeder extends BaseSeeder
{
    const JSON_PATH = 'database/seeders/data/dishes_seeds.json';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = $this->getSeedsFromJsonOrFail(self::JSON_PATH);

        foreach ($seeds as $seed) {
            unset($seed['id']);
            (new Dish($seed))->save();
        }
    }
}
