<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createSession(): TestResponse {
        $response = $this->post('/api/v1/signup', [
            "name" => "Sheila Ricalde",
            "email" => "sheilaricalde@gmail.com",
            "password" => "passw0rd"
        ]);

        $response->assertStatus(201);

        $loginResponse = $this->post('/api/v1/login', [
            "email" => "sheilaricalde@gmail.com",
            "password" => "passw0rd"
        ]);

        //dd($loginResponse->json());

        $loginResponse->assertStatus(200);

        return $loginResponse;
    }

    protected function authHeader(TestResponse $response): array {
        return [
            "Authorization" => "Bearer " . $response->json()["token"]
        ];
    }
}
