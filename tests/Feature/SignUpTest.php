<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class SignUpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SIGNUP-01
     */
    public function test_client_can_create_user()
    {
        $user = [
            "name" => "nuevousuario",
            "email" => "nuevousuario@correo.com",
            "password" => "unodostres123"
        ];
        $response = $this->json('POST', '/api/v1/signup', $user);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                "data" => [
                    'type' => 'Users',
                    'attributes' => [
                        'name' => $user['name'],
                        'email' => $user["email"]
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

        $this->json('POST','/api/v1/signup',$usuario);
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
}