<?php

namespace Database\Seeders;

use App\Models\Product;

class ProductSeeder extends BaseSeeder
{
    const JSON_PATH = 'database/seeders/data/products_seeds.json';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = $this->getSeedsFromJsonOrFail(self::JSON_PATH);

        foreach ($seeds as $seed) {
            unset($seed['id'], $seed['parts']);
            (new Product($seed))->save();
        }
    }
}
