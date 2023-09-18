<?php

namespace Feature\Api;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSoc;
use App\Models\ProductSocTransformer;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductSocControllerTest extends TestCase
{
    public function test_get_all_product_socs(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->create())
                         ->getJson(route('api.product-socs.index'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));

        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'title',
                    'soc_standard',
                    'transformers' => [
                        [
                            'id',
                            'product_soc_id',
                            'soc_origin',
                            'coefficient',
                            'coefficient_calc',
                        ]

                    ]
                ]
            ]
        ]);
    }

    public function test_get_product_soc(): void
    {
        $product = Product::factory()->create();
        $this->assertModelExists($product);

        $productSoc = ProductSoc::find($product->product_soc_id);
        $this->assertModelExists($productSoc);

        $response = $this->actingAs(User::factory()->create())
                         ->getJson(route('api.product-socs.show', ['product_soc' => $productSoc->getKey()]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'soc_standard',
                'transformers' => [
                    [
                        'id',
                        'product_soc_id',
                        'soc_origin',
                        'coefficient',
                        'coefficient_calc',
                    ]
                ]
            ]
        ]);
    }

    public function test_create_product_soc_unauthorized(): void
    {
        $soc = ProductSoc::factory()->make();
        $this->assertModelMissing($soc);

        $payload = $soc->toArray();
        $payload['transformers'] = ProductSocTransformer::factory()
                                             ->count($this->faker->numberBetween(1, 5))
                                             ->make()
                                             ->map(function ($transformer) {
                                                 unset($transformer['product_soc_id']);
                                                 return $transformer;
                                             })->toArray();

        $response = $this->postJson(route('api.product-socs.store'), $payload);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_create_product_soc_authorized(): void
    {
        $payload = ProductSoc::factory()->make()->toArray();
        $transformers = ProductSocTransformer::factory()
                             ->count($this->faker->numberBetween(1, 5))
                             ->make()
                             ->map(function ($transformer) {
                                unset($transformer['product_soc_id']);
                                return $transformer;
                            })->toArray();
        $payload['transformers'] = $transformers;

        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.product-socs.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);

        unset($payload['transformers']);
        $this->assertDatabaseHas('product_socs', $payload);

        foreach ($transformers as $transformer){
            $this->assertDatabaseHas('product_soc_transformers', $transformer);
        }
    }

    public function test_update_product_soc(): void
    {
        $soc = ProductSoc::factory()->create();
        $this->assertModelExists($soc);

        $transformers = ProductSocTransformer::factory()
                                            ->state([
                                                'product_soc_id' => $soc->getKey()
                                            ])
                                             ->count($this->faker->numberBetween(1, 5))
                                             ->create();

        $payload = $soc->toArray();
        $payloadTransformers = $transformers->toArray();

        $payloadTransformersNew = ProductSocTransformer::factory()
                                             ->count($this->faker->numberBetween(1, 3))
                                             ->make()
                                             ->map(function ($transformer) {
                                                 unset($transformer['product_soc_id'], $transformer['id']);
                                                 return $transformer;
                                             })->toArray();

        $payloadTransformers = array_merge($payloadTransformers, $payloadTransformersNew);

        $payload['transformers'] = $payloadTransformers;

        $response = $this->actingAs(User::factory()->create())
                         ->putJson(route('api.product-socs.update', [
                             'product_soc' => $soc->getKey()
                         ]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);

        unset($payload['transformers']);
        $this->assertDatabaseHas('product_socs', $payload);

        foreach ($payloadTransformers as $transformer){
            $this->assertDatabaseHas('product_soc_transformers', $transformer);
        }
    }

    public function test_destroy_product_soc(): void
    {
        $socs = ProductSoc::factory()->count($this->faker->numberBetween(1, 5))->create();
        $productSocToDelete = $socs->random(1)->first();
        $this->assertModelExists($productSocToDelete);

        $response = $this->actingAs(User::factory()->create())
                         ->delete(route('api.product-socs.destroy', [
                             'product_soc' => $productSocToDelete->getKey()
                         ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertModelMissing($productSocToDelete);
    }
}
