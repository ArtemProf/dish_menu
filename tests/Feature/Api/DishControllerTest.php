<?php

namespace Tests\Feature\Api;

use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DishControllerTest extends TestCase
{
    public function test_get_all_dishes(): void
    {
        $user = User::factory()->create();
        Dish::factory()->count(3)->create();

        $response = $this->actingAs($user)
                         ->getJson(route('api.dishes.index'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'dish_category_id',
                    'title',
                    'video',
                    'tag',
                    'image',
                    'description',
                    'nutritional_value',
                    'exclaim',
                    //                    'ingredients',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
    }

    public function test_get_dish(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson(route('api.dishes.show', ['dish' => $dish->getKey()]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'dish_category_id',
                'title',
                'video',
                'tag',
                'image',
                'description',
                'nutritional_value',
                'exclaim',
                //                'ingredients',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_create_dish_unauthorized(): void
    {
        $category               = DishCategory::factory()->create();
        $dishCountBeforeRequest = Dish::count();

        $response = $this->postJson(route('api.dishes.store'), [
            'dish_category_id'  => $category->getKey(),
            'title'             => $this->faker->words(3, true),
            'video'             => $this->faker->url(),
            'tag'               => $this->faker->word(),
            'image'             => $this->faker->imageUrl(),
            'description'       => $this->faker->text(),
            'nutritional_value' => $this->faker->words(10, true),
            'exclaim'           => $this->faker->text(),
            //            'ingredients' => [],
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseCount('dishes', $dishCountBeforeRequest);
    }

    public function test_create_dish_authorized(): void
    {
        $category = DishCategory::factory()->create();
        $payload  = [
            'dish_category_id'  => $category->getKey(),
            'title'             => $this->faker->words(3, true),
            'video'             => $this->faker->url(),
            'tag'               => $this->faker->word(),
            'image'             => $this->faker->imageUrl(),
            'description'       => $this->faker->text(),
            'nutritional_value' => $this->faker->words(10, true),
            'exclaim'           => $this->faker->text(),
            //            'ingredients' => [],
        ];

        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.dishes.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('dishes', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_dish(): void
    {
        $user     = User::factory()->create();
        $dish     = Dish::factory()->create();
        $category = DishCategory::factory()->create();

        $payload = [
            'dish_category_id'  => $category->getKey(),
            'title'             => $this->faker->words(3, true),
            'video'             => $this->faker->url(),
            'tag'               => $this->faker->word(),
            'image'             => $this->faker->imageUrl(),
            'description'       => $this->faker->text(),
            'nutritional_value' => $this->faker->words(10, true),
            'exclaim'           => $this->faker->text(),
            //            'ingredients' => [],
        ];

        $response = $this->actingAs($user)
                         ->putJson(route('api.dishes.update', ['dish' => $dish->getKey()]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('dishes', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_destroy_dish(): void
    {
        $dishes         = Dish::factory()->count(2)->create();
        $dishToDeleteId = $dishes->random(1)->first()->getKey();

        $this->assertDatabaseHas('dishes', [
            'id' => $dishToDeleteId
        ]);

        $response = $this->actingAs(User::factory()->create())
                         ->delete(route('api.dishes.destroy', [
                             'dish' => $dishToDeleteId
                         ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('dishes', [
            'id' => $dishToDeleteId
        ]);
    }

    public function test_dish_recognize_image_success(): void
    {
        Http::preventStrayRequests();

        $service = config('services.ocr.service');
        $this->assertEquals('ocr_space', $service);

        $serviceUrl = config('services.ocr.base_url');
        Http::fake([
            $serviceUrl => static::getHttpResponseFromTestData('ocr_space_success.json')
        ]);

        $payload  = [
            'image' => UploadedFile::fake()->image('page.jpg', 1024, 1024)
        ];
        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.dishes.ocr'), $payload);

        $response->assertOk()
                 ->assertJson(fn(AssertableJson $json) =>
                    $json->has('data', fn($json) =>
                        $json->whereType('text', 'string')
                    )
                 );
    }

    public function test_dish_recognize_image_error(): void
    {
        Http::preventStrayRequests();

        $service = config('services.ocr.service');
        $this->assertEquals('ocr_space', $service);

        $serviceUrl = config('services.ocr.base_url');
        Http::fake([
            $serviceUrl => static::getHttpResponseFromTestData('ocr_space_error.json')
        ]);

        $payload  = [
            'image' => UploadedFile::fake()->image('page.jpg', 1024, 1024)
        ];
        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.dishes.ocr'), $payload);

        $response->assertStatus(Response::HTTP_CONFLICT)
                 ->assertJson(fn(AssertableJson $json) =>
                    $json->has('data', fn( AssertableJson$json) =>
                        $json->missing('text')
                    ) &&
                    $json->whereType('message', 'string')
                 );
    }
}
