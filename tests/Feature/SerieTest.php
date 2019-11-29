<?php

namespace Tests\Feature;

use App\Package;
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

    public function test_show_one_serie(){
        $serie = factory(Serie::class)->create();

        $response = $this->get('/api/v1/series/' . $serie->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name'=> $serie->name,
                'description'=> $serie->description,
                'image'=>$serie->image,
            ]);
    }

    public function test_show_with_uncreated_serie()
    {
        factory(Serie::class)->create();

        $response = $this->get('/api/v1/series/2');

        $response->assertStatus(404);
    }

    public function test_list_series_first_serie_returns_with_three_packages(){
        factory(Serie::class)->create();
        factory(Package::class, 3)
            ->create()
            ->each(function ($package) {
                $package->series()->attach(Serie::all()->first());
            });

        $response = $this->get('/api/v1/series/' . Serie::all()->first()->id);

        $response
            ->assertStatus(200)
            ->assertJsonCount(3, 'packages');
    }

}
