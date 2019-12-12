<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_user()
    {
        $user = [
            "name" => "user",
            "email" => "user@correo.com",
            "password" => "unodostres123"
        ];

        $response = $this->json('POST', 'api/v1/signup', $user);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'type',
                    'attributes' => [
                        'name',
                        'email'
                    ]
                ]
            ]);
    }

    /**
     * SIGNUP-02
     */
    public function test_client_can_not_create_user_without_name()
    {
        $user = [
            "email" => "nuevousuario@correo.com",
            "password" => "unodostres123"
        ];
        $response = $this->json('POST', '/api/v1/signup', $user);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                "errors" => [
                    "code" => "ERROR-1",
                    "title" => "Unprocessable Entity",
                    "message" => "Un atributo enviado no es correcto"
                ]
            ]);
    }

    /**
     * SIGNUP-03
     */
    public function test_client_can_not_create_user_without_email()
    {
        $user = [
            "name" => "nuevousuario",
            "password" => "unodostres123"
        ];
        $response = $this->json('POST', '/api/v1/signup', $user);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                "errors" => [
                    "code" => "ERROR-1",
                    "title" => "Unprocessable Entity",
                    "message" => "Un atributo enviado no es correcto"
                ]
            ]);
    }

    /**
     * SIGNUP-04
     */
    public function test_client_can_not_create_user_without_email_format()
    {
        $user = [
            "name" => "nuevousuario",
            "email" => "nuevousuarioarrobacorreopuntocom",
            "password" => "unodostres123"
        ];
        $response = $this->json('POST', '/api/v1/signup', $user);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                "errors" => [
                    "code" => "ERROR-1",
                    "title" => "Unprocessable Entity",
                    "message" => "Un atributo enviado no es correcto"
                ]
            ]);
    }

    /**
     * SIGNUP-05
     */
    public function test_client_can_not_create_user_with_email_already_taken()
    {
        $usuario = [
            "name" => "usuario",
            "email" => "nuevousuario@correo.com",
            "password" => "unodostres123"
        ];

        $user = [
            "name" => "nuevousuario",
            "email" => "nuevousuario@correo.com",
            "password" => "unodostres123"
        ];

        $this->json('POST', '/api/v1/signup', $usuario);
        $response = $this->json('POST', '/api/v1/signup', $user);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                "errors" => [
                    "code" => "ERROR-1",
                    "title" => "Unprocessable Entity",
                    "message" => "Un atributo enviado no es correcto"
                ]
            ]);
    }

    /**
     * SIGNUP-06
     */
    public function test_client_can_not_create_user_without_password()
    {
        $user = [
            "name" => "nuevousuario",
            "email" => "nuevousuario@correo.com",
        ];
        $response = $this->json('POST', '/api/v1/signup', $user);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                "errors" => [
                    "code" => "ERROR-1",
                    "title" => "Unprocessable Entity",
                    "message" => "Un atributo enviado no es correcto"
                ]
            ]);
    }

    /**
     * SIGNUP-07
     */
    public function test_client_can_not_create_user_with_short_password()
    {
        $user = [
            "name" => "nuevousuario",
            "email" => "nuevousuario@correo.com",
            "password" => "uno"
        ];
        $response = $this->json('POST', '/api/v1/signup', $user);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                "errors" => [
                    "code" => "ERROR-1",
                    "title" => "Unprocessable Entity",
                    "message" => "Un atributo enviado no es correcto"
                ]
            ]);
    }

    public function test_logout_without_token_returns_error_message()
    {
        $response = $this->post('/api/v1/logout');

        $response
            ->assertStatus(401)
            ->assertJsonFragment(
                ['error' => 'Token not provided']
            );
    }

    public function test_refresh_without_token_returns_error_message()
    {
        $response = $this->post('/api/v1/refresh');

        $response
            ->assertStatus(401)
            ->assertJsonFragment(
                ['error' => 'Token not provided']
            );
    }

    public function test_me_without_token_returns_error_message()
    {
        $response = $this->get('/api/v1/me');

        $response
            ->assertStatus(401)
            ->assertJsonFragment(
                ['error' => 'Token not provided']
            );
    }

    public function test_login_should_return_a_token()
    {
        $response = $this->post('/api/v1/signup', [
            "name" => "Sheila Ricalde",
            "email" => "sheilaricalde@gmail.com",
            "password" => "passw0rd"
        ]);

        $response->assertStatus(201);

        $response = $this->post('/api/v1/login', [
            "email" => "sheilaricalde@gmail.com",
            "password" => "passw0rd"
        ]);

        $response->assertStatus(200);
    }

    public function test_me_should_return_my_logged_user()
    {
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

        $loginResponse->assertStatus(200);

        $response = $this->get('/api/v1/me', $this->authHeader($loginResponse));

        $response->assertJsonStructure([
            'data' => [
                'type',
                'attributes' => [
                    'name',
                    'email'
                ]
            ]
        ]);
    }
}
