<?php

namespace Feature\Api;

use App\Models\DishIngredient;
use App\Models\DishIngredientCategory;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DishIngredientCategoryControllerTest extends TestCase
{
    public function test_get_all_dish_ingredient_categories(): void
    {
        $user = User::factory()->create();
        DishIngredientCategory::factory()->count(3)->create();

        $response = $this->actingAs($user)
                         ->getJson(route('api.dish-ingredient-categories.index'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'is_hidden',
                    'sort',
                ]
            ]
        ]);
    }

    public function test_get_dish_ingredient_category(): void
    {
        $user = User::factory()->create();
        $dishCategory = DishIngredientCategory::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson(route('api.dish-ingredient-categories.show', ['dish_ingredient_category' => $dishCategory->getKey()]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'is_hidden',
                'sort',
            ]
        ]);
    }

    public function test_create_dish_ingredient_category_unauthorized(): void
    {
        $dishCategoryCountBeforeRequest = DishIngredientCategory::count();

        $response = $this->postJson(route('api.dish-ingredient-categories.store'), [
            'title' => $this->faker->words(3, true),
            'sort'  => $this->faker->numberBetween(1, 10),
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseCount('dish_ingredient_categories', $dishCategoryCountBeforeRequest);
    }

    public function test_create_dish_ingredient_category_authorized(): void
    {
        $payload = [
            'title' => $this->faker->words(3, true),
            'sort'  => $this->faker->numberBetween(1, 10),
        ];

        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.dish-ingredient-categories.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('dish_ingredient_categories', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_dish_ingredient_category(): void
    {
        $user = User::factory()->create();
        $dishCategory = DishIngredientCategory::factory()->create();

        $payload = [
            'title' => $this->faker->words(3, true),
            'sort'  => $this->faker->numberBetween(1, 10),
        ];

        $response = $this->actingAs($user)
                         ->putJson(route('api.dish-ingredient-categories.update', ['dish_ingredient_category' => $dishCategory->getKey()]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('dish_ingredient_categories', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_destroy_dish_ingredient_category(): void
    {
        $dishCategories         = DishIngredientCategory::factory()->count(2)->create();
        $dishCategoryToDeleteId = $dishCategories->random(1)->first()->getKey();

        $this->assertDatabaseHas('dish_ingredient_categories', [
            'id' => $dishCategoryToDeleteId
        ]);

        $response = $this->actingAs(User::factory()->create())
                         ->delete(route('api.dish-ingredient-categories.destroy', [
                             'dish_ingredient_category' => $dishCategoryToDeleteId
                         ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('dish_ingredient_categories', [
            'id' => $dishCategoryToDeleteId
        ]);
    }
}
