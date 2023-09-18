<?php

namespace Feature\Api;

use App\Models\CookListItem;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DishCategoryControllerTest extends TestCase
{
    public function test_get_all_dish_categories(): void
    {
        $user = User::factory()->create();

        DishCategory::factory()->count(3)->create();

        $response = $this->actingAs($user)
                         ->getJson(route('api.dish-categories.index'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'title',
                    'sort',
                ]
            ]
        ]);
    }

    public function test_get_dish_category(): void
    {
        $user = User::factory()->create();
        $dishCategory = DishCategory::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson(route('api.dish-categories.show', ['dish_category' => $dishCategory->getKey()]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'sort',
            ]
        ]);
    }

    public function test_create_dish_category_unauthorized(): void
    {
        $dishCategoryCountBeforeRequest = DishCategory::count();

        $response = $this->postJson(route('api.dish-categories.store'), [
            'title' => $this->faker->words(3, true),
            'sort'  => $this->faker->numberBetween(1, 10),
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseCount('dish_categories', $dishCategoryCountBeforeRequest);
    }

    public function test_create_dish_category_authorized(): void
    {
        $payload = [
            'title' => $this->faker->words(3, true),
            'sort'  => $this->faker->numberBetween(1, 10),
        ];

        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.dish-categories.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('dish_categories', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_dish_category(): void
    {
        $user = User::factory()->create();
        $dishCategory = DishCategory::factory()->create();

        $payload = [
            'title' => $this->faker->words(3, true),
            'sort'  => $this->faker->numberBetween(1, 10),
        ];

        $response = $this->actingAs($user)
                         ->putJson(route('api.dish-categories.update', ['dish_category' => $dishCategory->getKey()]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('dish_categories', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_destroy_dish_category(): void
    {
        $dishCategories         = DishCategory::factory()->count(2)->create();
        $dishCategoryToDeleteId = $dishCategories->random(1)->first()->getKey();

        $this->assertDatabaseHas('dish_categories', [
            'id' => $dishCategoryToDeleteId
        ]);

        $response = $this->actingAs(User::factory()->create())
                         ->delete(route('api.dish-categories.destroy', [
                             'dish_category' => $dishCategoryToDeleteId
                         ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('dish_categories', [
            'id' => $dishCategoryToDeleteId
        ]);
    }
}
