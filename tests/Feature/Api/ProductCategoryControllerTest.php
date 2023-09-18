<?php

namespace Feature\Api;

use App\Models\Dish;
use App\Models\ProductCategory;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductCategoryControllerTest extends TestCase
{
    public function test_get_all_product_categories(): void
    {
        ProductCategory::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->create())
                         ->getJson(route('api.product-categories.index'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'parent_id',
                    'title',
                ]
            ]
        ]);
    }

    public function test_get_product_category(): void
    {
        $dishCategory = ProductCategory::factory()->create();

        $response = $this->actingAs(User::factory()->create())
                         ->getJson(route('api.product-categories.show', ['product_category' => $dishCategory->getKey()]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'parent_id',
                'title',
            ]
        ]);
    }

    public function test_create_product_category_unauthorized(): void
    {
        $dishCategoryCountBeforeRequest = ProductCategory::count();

        $response = $this->postJson(route('api.product-categories.store'), [
            'title' => $this->faker->words(3, true),
            'sort'  => $this->faker->numberBetween(1, 10),
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseCount('product_categories', $dishCategoryCountBeforeRequest);
    }

    public function test_create_product_category_authorized(): void
    {
        $payload = [
            'title' => $this->faker->words(3, true),
            'sort'  => $this->faker->numberBetween(1, 10),
        ];

        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.product-categories.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('product_categories', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_product_category(): void
    {
        $user = User::factory()->create();
        $dishCategory = ProductCategory::factory()->create();

        $payload = [
            'title' => $this->faker->words(3, true),
            'sort'  => $this->faker->numberBetween(1, 10),
        ];

        $response = $this->actingAs($user)
                         ->putJson(route('api.product-categories.update', ['product_category' => $dishCategory->getKey()]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('product_categories', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_destroy_product_category(): void
    {
        $dishCategories         = ProductCategory::factory()->count(2)->create();
        $dishCategoryToDeleteId = $dishCategories->random(1)->first()->getKey();

        $this->assertDatabaseHas('product_categories', [
            'id' => $dishCategoryToDeleteId
        ]);

        $response = $this->actingAs(User::factory()->create())
                         ->delete(route('api.product-categories.destroy', [
                             'product_category' => $dishCategoryToDeleteId
                         ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('product_categories', [
            'id' => $dishCategoryToDeleteId
        ]);
    }
}
