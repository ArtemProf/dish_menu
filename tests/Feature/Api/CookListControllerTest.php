<?php

namespace Feature\Api;

use App\Models\CookList;
use App\Models\CookListItem;
use App\Models\Dish;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CookListControllerTest extends TestCase
{
    public function test_get_all_cook_lists(): void
    {
        $user     = User::factory()->create();
        $cookList = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->create();
        $this->assertModelExists($cookList);

        $cookListItems = CookListItem::factory()->state([
            'cook_list_id' => $cookList->getKey()
        ])->count(3)->create();
        $this->assertModelExists($cookListItems[0]);

        $response = $this->actingAs($user)
                         ->getJson(route('api.cook-lists.index'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'title',
                    'is_default',
                    'items' => [
                        [
                            'id',
                            'cook_list_id',
                            'dish_id',
                            'user_id',
                            'amount',
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function test_get_cook_list(): void
    {
        $user     = User::factory()->create();
        $cookList = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->create();
        $this->assertModelExists($cookList);

        $cookListItems = CookListItem::factory()->state([
            'cook_list_id' => $cookList->getKey()
        ])->count(3)->create();
        $this->assertModelExists($cookListItems[0]);

        $response = $this->actingAs($user)
                         ->getJson(route('api.cook-lists.show', ['cook_list' => $cookList->getKey()]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'is_default',
                'items' => [
                    [
                        'id',
                        'cook_list_id',
                        'dish_id',
                        'user_id',
                        'amount',
                    ]
                ]
            ]
        ]);
    }

    public function test_get_default_cook_list(): void
    {
        $user     = User::factory()->create();
        CookList::factory()->state([
            'user_id' => $user->getKey(),
            'is_default' => false
        ])->create();
        $cookList = CookList::factory()->state([
            'user_id' => $user->getKey(),
            'is_default' => true
        ])->create();
        $this->assertModelExists($cookList);

        $cookListItems = CookListItem::factory()->state([
            'cook_list_id' => $cookList->getKey()
        ])->count(3)->create();
        $this->assertModelExists($cookListItems[0]);

        $response = $this->actingAs($user)
                         ->getJson(route('api.cook-lists.show', ['cook_list' => -1]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'title',
                'is_default',
                'created_at',
                'updated_at',
                'items' => [
                    [
                        'id',
                        'cook_list_id',
                        'dish_id',
                        'user_id',
                        'amount',
                    ]
                ]
            ]
        ]);
    }

    public function test_get_default_cook_list_with_autocreating(): void
    {
        $response = $this->actingAs(User::factory()->create())
                         ->getJson(route('api.cook-lists.show', ['cook_list' => -1]));

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertNotEmpty($response->json('data'));
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'title',
                'is_default',
                'created_at',
                'updated_at',
                'items'
            ]
        ]);
    }

    public function test_create_cook_list_unauthorized(): void
    {
        $user    = User::factory()->create();
        $payload = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->make()->toArray();
        $productCountBeforeRequest = CookList::count();
        unset($payload['user_id']);

        $payload['items'] = CookListItem::factory()->count(3)
                                        ->make()
                                        ->map(function ($item) {
                                            unset($item['cook_list_id'], $item['user_id']);
                                            return $item;
                                        })->toArray();

        $response = $this->postJson(route('api.cook-lists.store'), $payload);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseCount('cook_lists', $productCountBeforeRequest + 3);
    }

    public function test_create_cook_list_authorized(): void
    {
        $user    = User::factory()->create();
        $payload = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->make()->toArray();
        unset($payload['user_id']);

        $payload['items'] = CookListItem::factory()->count(3)
                                        ->make()
                                        ->map(function ($item) {
                                            unset($item['cook_list_id'], $item['user_id']);
                                            return $item;
                                        })->toArray();

        $response = $this->actingAs($user)
                         ->postJson(route('api.cook-lists.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $items = $payload['items'];
        unset($payload['items']);

        $this->assertDatabaseHas('cook_lists', $payload);
        foreach ($items as $item) {

            $this->assertDatabaseHas('cook_list_items', $item);
        }

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_create_cook_list_authorized_without_items(): void
    {
        $user    = User::factory()->create();
        $payload = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->make()->toArray();
        unset($payload['user_id'], $payload['items']);

        $response = $this->actingAs($user)
                         ->postJson(route('api.cook-lists.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('cook_lists', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_create_cook_list_authorized_with_items(): void
    {
        $user    = User::factory()->create();
        $payload = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->make()->toArray();
        unset($payload['user_id']);

        $payload['items'] = CookListItem::factory()->count(3)
                                        ->make()
                                        ->map(function ($item) {
                                            unset($item['cook_list_id'], $item['user_id']);
                                            return $item;
                                        })->toArray();

        $response = $this->actingAs($user)
                         ->postJson(route('api.cook-lists.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $items = $payload['items'];
        unset($payload['items']);

        $this->assertDatabaseHas('cook_lists', $payload);
        foreach ($items as $item) {
            $this->assertDatabaseHas('cook_list_items', $item);
        }

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_cook_list_authorized_without_items(): void
    {
        $user    = User::factory()->create();
        $cookList = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->create();

        $this->assertModelExists($user);
        $this->assertModelExists($cookList);

        $payload = $cookList->toArray();
        $payload['title'] = $this->faker->words(3, true);
        unset($payload['user_id'], $payload['items']);

        $response = $this->actingAs($user)
                         ->putJson(route('api.cook-lists.update', [
                             'cook_list' => $cookList->getKey()
                         ]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('cook_lists', $payload);

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_update_cook_list_authorized_with_items(): void
    {
        $user    = User::factory()->create();
        $cookList = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->create();
        $cookListItems = CookListItem::factory()->state([
            'user_id' => $user->getKey()
        ])->count(3)->create();

        $this->assertModelExists($user);
        $this->assertModelExists($cookList);
        foreach ($cookListItems as $cookListItem) {
            $this->assertModelExists($cookListItem);
        }

        $payload = $cookList->toArray();
        $payload['title'] = $this->faker->words(3, true);
        unset($payload['user_id'], $payload['items']);

        $payload['items'] = $cookListItems->map(function ($item) {
                                            $item['dish_id'] = Dish::inRandomOrder()->take(1)->value('id');
                                            $item['amount'] = $this->faker->numberBetween(1, 100);
                                            return $item;
                                        })->toArray();

        $response = $this->actingAs($user)
                         ->putJson(route('api.cook-lists.update', [
                             'cook_list' => $cookList->getKey()
                         ]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $items = $payload['items'];
        unset($payload['items']);

        $this->assertDatabaseHas('cook_lists', $payload);
        foreach ($items as $item) {
            $this->assertDatabaseHas('cook_list_items', $item);
        }

        $response->assertJsonStructure([
            'data' => array_keys($payload)
        ]);
    }

    public function test_destroy_cook_list_authorized_without_items(): void
    {
        $user    = User::factory()->create();
        $cookList = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->create();

        $this->assertModelExists($user);
        $this->assertModelExists($cookList);

        $response = $this->actingAs($user)
                         ->delete(route('api.cook-lists.destroy', [
                             'cook_list' => $cookList->getKey()
                         ]));

        $response->assertStatus(Response::HTTP_OK);

        $this->assertModelMissing($cookList);
    }

    public function test_destroy_cook_list_authorized_with_items(): void
    {
        $user    = User::factory()->create();
        $cookList = CookList::factory()->state([
            'user_id' => $user->getKey()
        ])->create();
        $cookListItems = CookListItem::factory()->state([
            'user_id'      => $user->getKey(),
            'cook_list_id' => $cookList->getKey()
        ])->count(3)->create();

        $this->assertModelExists($user);
        $this->assertModelExists($cookList);
        foreach ($cookListItems as $cookListItem) {
            $this->assertModelExists($cookListItem);
        }

        $response = $this->actingAs($user)
                         ->delete(route('api.cook-lists.destroy', [
                             'cook_list' => $cookList->getKey()
                         ]));

        $response->assertStatus(Response::HTTP_OK);

        $this->assertModelMissing($cookList);
        foreach ($cookListItems as $cookListItem) {
            $this->assertModelMissing($cookListItem);
        }
    }
}
