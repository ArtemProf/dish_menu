<?php

namespace Feature\Api;

use App\Models\CookList;
use App\Models\CookListItem;
use App\Models\Dish;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CookListItemControllerTest extends TestCase
{
    public function test_get_all_cook_list_item(): void
    {
        $user = User::factory()->create();
        $cookListItems = CookListItem::factory()->state([
            'user_id' => $user->getKey()
        ])->count(3)
          ->create();

        $this->assertModelExists($user);
        $cookListItems->map(fn($cookListItem) => $this->assertModelExists($cookListItem));

        $response = $this->actingAs($user)
                         ->getJson(route('api.cook-list-items.index'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'user_id',
                    'cook_list_id',
                    'dish_id',
                    'amount',
                ]
            ]
        ]);
    }

    public function test_get_cook_list_item(): void
    {
        $user = User::factory()->create();
        $cookListItem = CookListItem::factory()->state([
            'user_id' => $user->getKey()
        ])->create();

        $this->assertModelExists($user);
        $this->assertModelExists($cookListItem);

        $response = $this->actingAs($user)
                         ->getJson(route('api.cook-list-items.show', [
                             'cook_list_item' => $cookListItem->getKey()
                         ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'cook_list_id',
                'dish_id',
                'amount',
            ]
        ]);
    }

    public function test_create_cook_list_item_unauthorized(): void
    {
        $user = User::factory()->create();
        $cookList = CookList::factory()->create();
        $cookListItem = CookListItem::factory()->state([
            'cook_list_id' => $cookList->getKey(),
            'user_id' => $user->getKey()
        ])->make();

        $this->assertModelExists($cookList);
        $this->assertModelMissing($cookListItem);

        $payload = $cookListItem->toArray();

        $response = $this->postJson(route('api.cook-list-items.store'), $payload);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('cook_list_items', $payload);
    }

    public function test_create_cook_list_item_authorized(): void
    {
        $user = User::factory()->create();
        $cookList = CookList::factory()->create();
        $cookListItem = CookListItem::factory()->state([
            'cook_list_id' => $cookList->getKey(),
            'user_id'      => $user->getKey()
        ])->make();

        $this->assertModelExists($user);
        $this->assertModelExists($cookList);
        $this->assertModelMissing($cookListItem);

        $payload = $cookListItem->toArray();

        $response = $this->actingAs($user)
                         ->postJson(route('api.cook-list-items.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('cook_list_items', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_create_default_cook_list_item_authorized(): void
    {
        $user = User::factory()->create();
        $cookListItem = CookListItem::factory()->state([
            'user_id'      => $user->getKey(),
        ])->make();

        $this->assertModelExists($user);
        $this->assertModelMissing($cookListItem);

        $payload = $cookListItem->toArray();
        unset($payload['cook_list_id']);

        $response = $this->actingAs($user)
                         ->postJson(route('api.cook-list-items.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('cook_lists', [
            'user_id'    => $user->getKey(),
            'is_default' => 1,
        ]);
        $this->assertDatabaseHas('cook_list_items', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_cook_list_item_authorized(): void
    {
        $user = User::factory()->create();
        $cookList = CookList::factory()->create();
        $cookListItem = CookListItem::factory()->state([
            'cook_list_id' => $cookList->getKey(),
            'user_id'      => $user->getKey()
        ])->create();

        $this->assertModelExists($user);
        $this->assertModelExists($cookList);
        $this->assertModelExists($cookListItem);

        $payload = $cookListItem->toArray();

        $payload['dish_id'] = Dish::inRandomOrder()->take(1)->first()->getKey();
        $payload['amount'] = $cookListItem->amount  + 1;

        $response = $this->actingAs($user)
                         ->putJson(route('api.cook-list-items.update', ['cook_list_item' => $cookListItem->getKey()]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('cook_list_items', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($cookListItem->toArray())
        ]);
    }

    public function test_destroy_cook_list_item(): void
    {
        $user = User::factory()->create();
        $cookList = CookList::factory()->create();
        $cookListItem = CookListItem::factory()->state([
            'cook_list_id' => $cookList->getKey(),
            'user_id'      => $user->getKey()
        ])->create();

        $this->assertModelExists($user);
        $this->assertModelExists($cookList);
        $this->assertModelExists($cookListItem);

        $response = $this->actingAs($user)
                         ->delete(route('api.cook-list-items.destroy', ['cook_list_item' => $cookListItem->getKey()]));

        $response->assertStatus(Response::HTTP_OK);

        $this->assertModelMissing($cookListItem);
    }
}
