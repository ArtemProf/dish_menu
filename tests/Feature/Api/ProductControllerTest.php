<?php

namespace Feature\Api;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSoc;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function test_get_all_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->create())
                         ->getJson(route('api.products.index'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'product_soc_id',
                    'product_category_id',
                    'title',
                ]
            ]
        ]);
    }

    public function test_get_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs(User::factory()->create())
                         ->getJson(route('api.products.show', ['product' => $product->getKey()]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'product_category_id',
                'product_soc_id',
                'title',
            ]
        ]);
    }

    public function test_create_product_unauthorized(): void
    {
        $soc                       = ProductSoc::factory()->create();
        $category                  = ProductCategory::factory()->create();
        $productCountBeforeRequest = Product::count();

        $response = $this->postJson(route('api.products.store'), [
            'product_soc_id'      => $soc->getKey(),
            'product_category_id' => $category->getKey(),
            'title'               => $this->faker->words(3, true),
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseCount('products', $productCountBeforeRequest);
    }

    public function test_create_product_authorized_without_soc(): void
    {
        $category = ProductCategory::factory()->create();
        $payload  = [
            'product_category_id' => $category->getKey(),
            'title'               => $this->faker->words(3, true),
        ];

        $this->assertModelExists($category);

        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.products.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('products', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_create_product_authorized_with_soc(): void
    {
        $soc = ProductSoc::factory()->create();
        $category = ProductCategory::factory()->create();
        $payload  = [
            'product_soc_id' => $soc->getKey(),
            'product_category_id' => $category->getKey(),
            'title'               => $this->faker->words(3, true),
        ];

        $this->assertModelExists($soc);
        $this->assertModelExists($category);

        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.products.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('products', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_product_without_soc(): void
    {
        $user     = User::factory()->create();
        $product  = Product::factory()->create();
        $category = ProductCategory::factory()->create();

        $this->assertModelExists($user);
        $this->assertModelExists($product);
        $this->assertModelExists($category);

        $payload = [
            'product_category_id' => $category->getKey(),
            'title'               => $this->faker->words(3, true),
        ];

        $response = $this->actingAs($user)
                         ->putJson(route('api.products.update', ['product' => $product->getKey()]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('products', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_product_with_soc(): void
    {
        $user     = User::factory()->create();
        $product  = Product::factory()->create();
        $category = ProductCategory::factory()->create();
        $soc = ProductSoc::factory()->create();

        $this->assertModelExists($user);
        $this->assertModelExists($product);
        $this->assertModelExists($category);
        $this->assertModelExists($soc);

        $payload = [
            'product_soc_id'      => $soc->getKey(),
            'product_category_id' => $category->getKey(),
            'title'               => $this->faker->words(3, true),
        ];

        $response = $this->actingAs($user)
                         ->putJson(route('api.products.update', ['product' => $product->getKey()]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('products', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_destroy_product(): void
    {
        $products          = Product::factory()->count(2)->create();
        $productToDeleteId = $products->random(1)->first()->getKey();

        $this->assertDatabaseHas('products', [
            'id' => $productToDeleteId
        ]);

        $response = $this->actingAs(User::factory()->create())
                         ->delete(route('api.products.destroy', [
                             'product' => $productToDeleteId
                         ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('products', [
            'id' => $productToDeleteId
        ]);
    }
}
