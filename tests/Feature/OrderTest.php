<?php


namespace Tests\Feature;


use App\Package;
use App\Serie;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_create_order_with_all_valid_data()
    {
        $this->withoutExceptionHandling();

        $packageOne = factory(Package::class)->create();
        $packageTwo = factory(Package::class)->create();

        $response = $this->post('/api/v1/orders/', [
            "order_items" => [
                ["package_id" => $packageOne->id],
                ["package_id" => $packageTwo->id]
            ]
        ], $this->authHeader($this->createSession()));

        $response->assertStatus(200)
            ->assertJsonStructure([

                'total',
                'user'


            ])->assertJsonFragment([
                'total' => $packageOne->price + $packageTwo->price
            ]);
    }

    public function test_fail_to_create_order_with_invalid_package_id()
    {
        $packageOne = factory(Package::class)->create();

        $response = $this->post('/api/v1/orders/', [
            "order_items" => [
                ["package_id" => $packageOne->id],
                ["package_id" => "1234"]
            ]
        ], $this->authHeader($this->createSession()));

        $response
            ->assertStatus(404)
            ->assertJsonFragment([
                "error" => "Some Package doesn't exists"
            ]);
    }
}
