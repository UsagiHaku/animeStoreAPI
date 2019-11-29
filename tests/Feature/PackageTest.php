<?php

namespace Tests\Feature;

use App\Package;
use App\Product;
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

}
