<?php

namespace Tests\Feature;

use App\Http\Resources\OrderResource;
use App\Package;
use App\Serie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class PackageTest extends OrderResource
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list_packages()
    {
        factory(Package::class, 3)->create();
        $response = $this->get('api/v1/packages',
            $this->authHeader($this->createSession())
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_show_one_package()
    {
        $package = factory(Package::class)->create();

        $response = $this->get('/api/v1/packages/' . $package->id,
            $this->authHeader($this->createSession())
        );

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => $package->title,
                'description' => $package->description,
                'image' => $package->image,
                'price' => strval($package->price)
            ]);
    }

    public function test_show_with_uncreated_package()
    {
        factory(Package::class)->create();

        $response = $this->get('/api/v1/packages/2',
            $this->authHeader($this->createSession())
        );

        $response->assertStatus(404);
    }

    public function test_list_packages_first_package_returns_with_three_series()
    {
        factory(Package::class)->create();
        factory(Serie::class, 3)
            ->create()
            ->each(function ($serie) {
                $serie->packages()->attach(Package::all()->first());
            });

        $response = $this->get('/api/v1/packages/' . Package::all()->first()->id,
            $this->authHeader($this->createSession())
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.series');
    }

    public function create_a_package_will_returns_the_created_package()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/v1/packages/', [
            'title' => 'Saga del  señor de los anillos',
            'description' => 'La mejor saga fantastica',
            'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg',
            'price' => '24.50',
            'series' => [
                [
                    'name' => 'El señor de los anillos 1',
                    'description' => 'Primera entrega de la saga fantastica',
                    'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg'
                ],
                [
                    'name' => 'El señor de los anillos 2',
                    'description' => 'Segunda entrega de la saga fantastica',
                    'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg'
                ]
            ]
        ], $this->authHeader($this->createSession()));

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Saga del señor de los anillos',
                'description' => 'La mejor saga fantastica',
                'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg',
                'price' => '24.50',
                'series' => [
                    [
                        'name' => 'El señor de los anillos 1',
                        'description' => 'Primera entrega de la saga fantastica',
                        'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg'
                    ],
                    [
                        'name' => 'El señor de los anillos 2',
                        'description' => 'Segunda entrega de la saga fantastica',
                        'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg'
                    ]
                ]
            ]);
    }


    public function test_update_a_package_will_returns_the_updated_package_without_update_series()
    {
        //$this->withoutExceptionHandling();
        $package = factory(Package::class)->create();

        $response = $this->put('/api/v1/packages/' . $package->id, [
            'title' => $package->title . 'nombre actualizado',
            'description' => $package->description . 'new',
            'image' => $package->image,
            'price' => $package->price
        ], $this->authHeader($this->createSession()));

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'title' => $package->title . 'nombre actualizado',
                'description' => $package->description . 'new',
                'image' => $package->image,
                'price' => $package->price
            ]);
    }

    public function test_update_a_package_adding_registered_series()
    {
        $package = factory(Package::class)->create();
        $serie = factory(Serie::class)->create();
        $package->series()->attach($serie);

        $newPackageSerie = factory(Serie::class)->create();
        $response = $this->put('/api/v1/packages/' . $package->id . '/series', [
            'series' => [
                [
                    'id' => $newPackageSerie->id,
                    'name' => $newPackageSerie->name,
                    'description' => $newPackageSerie->description,
                    'image' => $newPackageSerie->image
                ]
            ]
        ], $this->authHeader($this->createSession()));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'attributes' => [
                        'title',
                        'description',
                        'image',
                        'price',

                    ],
                    'series'
                ]
            ]);
    }

    public function test_update_a_package_removing_registered_series()
    {
        $package = factory(Package::class)->create();
        $serieOne = factory(Serie::class)->create();
        $serieTwo = factory(Serie::class)->create();
        $package->series()->attach($serieOne);
        $package->series()->attach($serieTwo);

        $response = $this->delete('/api/v1/packages/' . $package->id . '/series', [
            'series' => [
                [
                    'id' => $serieTwo->id,
                    'name' => $serieTwo->name,
                    'description' => $serieTwo->description,
                    'image' => $serieTwo->image
                ]
            ]
        ], $this->authHeader($this->createSession()));

        $response->assertStatus(204);
    }


    public function test_destroy_a_package()
    {
        $package = factory(Package::class)->create();

        $serie = factory(Serie::class)->create();
        $package->series()->attach($serie);

        $response = $this->delete('/api/v1/packages/' . $package->id,
            [], $this->authHeader($this->createSession()));

        $response->assertStatus(204);
        $this->assertDatabaseMissing("packages", ["id" => $package->id]);
    }

    public function test_get_all_series_of_one_package(){
        $package = factory(Package::class)->create();
        factory(Serie::class, 3)
            ->create()
            ->each(function ($serie) {
                $serie->packages()->attach(Package::all()->first());
            });
        $response = $this->get('/api/v1/packages/'. $package->id .'/series',
            $this->authHeader($this->createSession())
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

}
