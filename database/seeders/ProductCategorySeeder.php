<?php

namespace Database\Seeders;

use App\Models\ProductCategory;

class ProductCategorySeeder extends BaseSeeder
{
    const JSON_PATH = 'database/seeders/data/product_categories_seeds.json';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = $this->getSeedsFromJsonOrFail(self::JSON_PATH);

        foreach ($seeds as $seed) {
            unset($seed['id']);
            (new ProductCategory($seed))->save();
        }
    }
}
