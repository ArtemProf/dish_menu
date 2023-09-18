<?php

namespace Feature\Api;

use App\Models\Dish;
use App\Models\DishIngredient;
use App\Models\DishIngredientCategory;
use App\Models\User;
use App\Enums\EnumDishIngredientType;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DishIngredientControllerTest extends TestCase
{
    public function test_get_all_dish_ingredients(): void
    {
        $user = User::factory()->create();
        DishIngredient::factory()->count(3)->create();

        $response = $this->actingAs($user)
                         ->getJson(route('api.dish-ingredients.index'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'dish_id',
                    'type',
                    'type_id',
                    'title',
                    'comment',
                    'amount',
                    'amount_soc',
                    'amount_origin',
                    'not_required',
                ]
            ]
        ]);
    }

    public function test_get_dish_ingredient(): void
    {
        $dishIngredient = DishIngredient::factory()->create();

        $response = $this->actingAs(User::factory()->create())
                         ->getJson(route('api.dish-ingredients.show', ['dish_ingredient' => $dishIngredient->getKey()]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'dish_id',
                'type',
                'type_id',
                'title',
                'comment',
                'amount',
                'amount_soc',
                'amount_origin',
                'not_required',
            ]
        ]);
    }

    public function test_create_dish_ingredient_unauthorized(): void
    {
        $dishIngredient                   = DishIngredient::factory()->make();
        $dishIngredientCountBeforeRequest = DishIngredient::count();

        $response = $this->postJson(route('api.dish-ingredients.store'), $dishIngredient->toArray());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseCount('dish_ingredients', $dishIngredientCountBeforeRequest);
    }

    public function test_create_dish_ingredient_authorized(): void
    {
        $payload = DishIngredient::factory()->make()->toArray();

        $response = $this->actingAs(User::factory()->create())
                         ->postJson(route('api.dish-ingredients.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('dish_ingredients', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_dish_ingredient(): void
    {
        $dishIngredient = DishIngredient::factory()->create();
        $user = User::factory()->create();

        $payload = [
            'dish_id'       => Dish::factory()->create()->getKey(),
            'type'          => $this->faker->randomElement(EnumDishIngredientType::getAllValues()),
            'type_id'       => $this->faker->numberBetween(1, 100),
            'title'         => $this->faker->words(3, true),
            'comment'       => $this->faker->words(3, true),
            'amount'        => $this->faker->numberBetween(0, 10),
            'amount_soc'    => $this->faker->word(),
            'amount_origin' => $this->faker->words(3, true),
            'not_required'  => $this->faker->boolean(),
        ];

        $response = $this->actingAs($user)
                         ->putJson(route('api.dish-ingredients.update', ['dish_ingredient' => $dishIngredient->getKey()]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('dish_ingredients', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_destroy_dish_ingredient(): void
    {
        $dishCategories           = DishIngredient::factory()->count(2)->create();
        $dishIngredientToDeleteId = $dishCategories->random(1)->first()->getKey();

        $this->assertDatabaseHas('dish_ingredients', [
            'id' => $dishIngredientToDeleteId
        ]);

        $response = $this->actingAs(User::factory()->create())
                         ->delete(route('api.dish-ingredients.destroy', [
                             'dish_ingredient' => $dishIngredientToDeleteId
                         ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('dish_ingredients', [
            'id' => $dishIngredientToDeleteId
        ]);
    }
}
