<?php

namespace Tests\Feature;

use App\Package;
use App\Serie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class PackageTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list_packages()
    {
        factory(Package::class,3)->create();
        $response = $this->get('api/v1/packages');

        $response
            ->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_show_one_package(){
        $package = factory(Package::class)->create();

        $response = $this->get('/api/v1/packages/' . $package->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                  'title'=> $package->title,
                  'description'=> $package->description,
                  'image'=>$package->image,
                  'price'=>strval($package->price)
            ]);
    }

    public function test_show_with_uncreated_package()
    {
        factory(Package::class)->create();

        $response = $this->get('/api/v1/packages/2');

        $response->assertStatus(404);
    }

    public function test_list_packages_first_package_returns_with_three_series(){
        factory(Package::class)->create();
        factory(Serie::class, 3)
            ->create()
            ->each(function ($serie) {
                $serie->packages()->attach(Package::all()->first());
            });

        $response = $this->get('/api/v1/packages/' . Package::all()->first()->id);

        $response
            ->assertStatus(200)
            ->assertJsonCount(3, 'series');
    }

    public function create_a_package_will_returns_the_created_package(){
        $this->withoutExceptionHandling();

        $response = $this->post('/api/v1/packages/',[
            'title' => 'Saga del  señor de los anillos',
            'description' => 'La mejor saga fantastica',
            'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg',
            'price' => '24.50',
            'series'=>[
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

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Saga del señor de los anillos',
                'description' => 'La mejor saga fantastica',
                'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg',
                'price' => '24.50',
                'series'=>[
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

    //public function test_update_a_package_will_returns_the_updated_package() {
    //            $package = factory(Package::class)->create();
    //            $response = $this->put('/api/v1/packages/'. $package->id, [
    //                'title' => 'El señor de los anillos',
    //                'description' => 'La mejor saga fantastica'
    //            ]);
    //            $response
    //                ->assertStatus(200)
    //                ->assertJsonFragment([
    //                    'name' => 'El señor de los anillos',
    //                    'description' => 'La mejor saga fantastica',
    //                ])
    //                ->assertJsonCount(6);
    //    }

    public function test_add_series_in_a_created_package(){
        $package = factory(Package::class)->create();
        $series = factory(Serie::class, 3)
            ->create()
            ->each(function ($serie) {
                $serie->packages()->attach(Package::all()->first());
            });

        $response = $this->post('/api/v1/packages/'. $package->id . '/series' ,[
            "series" => [
                [
                    'name' => 'El señor de los anillos 1',
                    'description' => 'Primera entrega de la saga fantastica',
                    'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg'
                ]
            ]
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                [
                    'name' => 'El señor de los anillos 1',
                    'description' => 'Primera entrega de la saga fantastica',
                    'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg'
                ]
            ]);
    }

}