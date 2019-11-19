<?php

namespace Tests\Feature;

use App\Serie;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SerieTest extends TestCase
{
    use RefreshDatabase;//cada que corra una prueba re hace la base de datos
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list_series()
    {
        factory(Serie::class,3)->create();
        $response = $this->get('api/v1/series');

        $response
            ->assertStatus(200)
            ->assertJsonCount(3);
    }
}
