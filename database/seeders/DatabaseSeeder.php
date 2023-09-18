<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new ProductCategorySeeder())->run();
        (new ProductSocSeeder())->run();
        (new ProductSeeder())->run();
        (new DishCategorySeeder())->run();
        (new DishSeeder())->run();
        (new DishIngredientCategorySeeder())->run();
        (new DishIngredientSeeder())->run();
        User::factory()->create();

        Artisan::call('cache:clear');
    }
}
