<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function authHeader(TestResponse $response) {
        return [
            "Authorization" => "Bearer " . $response->json()["token"]
        ];
    }
}
