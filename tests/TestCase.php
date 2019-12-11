<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function createSession(): TestResponse {
        $user = factory(User::class)->create([
            "name" => "Sheila Ricalde",
            "email" => "sheilaricalde@gmail.com",
            "password" => bcrypt("passw0rd")
        ]);

        $loginResponse = $this->post('/api/v1/login', [
            "email" => $user->email,
            "password" => "passw0rd"
        ]);

        $loginResponse->assertStatus(200);

        return $loginResponse;
    }

    protected function myUser(TestResponse $response): User {
        $response = $this->get('/api/v1/me',
            $this->authHeader($response)
        );

        return User::find($response->json('data.attributes.id'));
    }

    protected function authHeader(TestResponse $response): array {
        return [
            "Authorization" => "Bearer " . $response->json()["token"]
        ];
    }
}
