<?php

namespace Tests\Feature;

use App\Package;
use App\Serie;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Category;
use App\Comment;
use App\User;

class SerieTest extends TestCase
{
    use RefreshDatabase;
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
    public function test_create_a_serie_will_returns_the_created_serie() {
                $response = $this->post('/api/v1/series', [
                    'name' => 'El señor de los anillos',
                    'description' => 'La mejor saga fantastica',
                    'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg'
                ]);

                $response
                    ->assertStatus(201)
                    ->assertJsonFragment([
                        'name' => 'El señor de los anillos',
                        'description' => 'La mejor saga fantastica',
                        'image' => 'https://i.ebayimg.com/images/g/RZwAAOSwLtxcp6WK/s-l1600.jpg'
                    ])
                   ->assertJsonCount(6);
    }

    public function test_update_a_serie_will_returns_the_updated_serie() {
            $serie = factory(Serie::class)->create();

            $response = $this->put('/api/v1/series/'. $serie->id, [
                'name' => 'El señor de los anillos',
                'description' => 'La mejor saga fantastica'
            ]);

            $response
                ->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'El señor de los anillos',
                    'description' => 'La mejor saga fantastica',
                    ])
                ->assertJsonCount(6);
    }

   public function test_delete_a_serie_will_returns_empty_response() {
            $package = factory(Package::class)->create();
            $category = factory(Category::class)->create();
            $user = factory(User::class)->create();
            $serie = factory(Serie::class)->create();
            $comment = factory(Comment::class)->create([
                    "serie_id" => $serie->id,
                    "user_id" => factory(User::class)->create()
                    ]);

                    $serie->comments()->save($comment);
            $serie->packages()->attach($package);
            $serie->categories()->attach($category);
            $serie->users()->attach($user);

            $response = $this->delete('/api/v1/series/'. $serie->id);

            $response->assertStatus(204);

            $this->assertDatabaseMissing("series", ["id" => $serie->id]);
        }


}
