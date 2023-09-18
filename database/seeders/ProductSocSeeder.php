<?php

namespace Database\Seeders;

use App\Models\ProductSoc;
use App\Models\ProductSocTransformer;

class ProductSocSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    const JSON_PATH = 'database/seeders/data/product_socs_seeds.json';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeds = $this->getSeedsFromJsonOrFail(self::JSON_PATH);

        foreach ($seeds as $seed) {
            $transformers = $seed['transformers'];
            unset($seed['id'], $seed['transformers']);
            $productSoc = new ProductSoc($seed);
            $productSoc->save();

            foreach ($transformers as $transformer) {
                $transformer['product_soc_id'] = $productSoc->id;
                (new ProductSocTransformer($transformer))->save();
            }
        }
    }
}
