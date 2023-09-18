<?php

namespace Feature\Api;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_user_register()
    {
        $response = $this->postJson(route('api.auth.register', [
            'email' => $this->faker->email,
            'password' => $this->faker->password(5, 50)
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'name'
            ]
        ]);
    }

    public function test_user_login_wrong_password_fails()
    {
        $email = $this->faker->email;
        $user = User::factory()->state([
            'email'    => $email,
        ])->create();
        $this->assertModelExists($user);

        $response = $this->postJson(route('api.auth.login', [
            'email' => $user->email,
            'password' => $this->faker->password(8, 50)
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_user_login()
    {

        $email = $this->faker->email;
        $password = $this->faker->password(8, 50);
        $user = User::factory()->state([
            'email'    => $email,
            'password' => $password,
        ])->create();
        $this->assertModelExists($user);

        $response = $this->postJson(route('api.auth.login', [
            'email' => $user->email,
            'password' => $password
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'token'
            ]
        ]);
    }

    public function test_user_logout()
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);

        $response = $this->actingAs($user)->postJson(route('api.auth.logout'));

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_check_get_dishes_with_token()
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);

        $response = $this->withToken($user->createToken('menu')->plainTextToken)
                         ->getJson(route('api.dishes.index'));

        $response->assertStatus(Response::HTTP_OK);
    }
}
