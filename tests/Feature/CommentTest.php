<?php

namespace Tests\Feature;

use App\Comment;
use App\Serie;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_comments_of_one_serie()
    {
       $serieWithComments = factory(Serie::class)->create();
       $user = factory(User::class)->create();
       $serieWithComments->users()->attach($user);

       $comment = factory(Comment::class)
            ->create([
                    "serie_id" => $serieWithComments->id,
                    "user_id" =>  $user->id
            ]);
       $serieWithComments->comments()->save($comment);

       $response = $this->get('api/v1/series/'. $serieWithComments->id. '/comments',
           $this->authHeader($this->createSession())
       );

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_create_a_comment_of_one_serie_will_returns_the_comment()
    {
        $this->withoutExceptionHandling();
        $serie= factory(Serie::class)->create();
        $user = factory(User::class)->create();
        $serie->users()->attach($user);

        $response = $this->post('api/v1/series/'. $serie->id. '/comments',[
            'description' => 'La mejor saga fantastica'

        ], $this->authHeader($this->createSession()));


        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'description' => 'La mejor saga fantastica'
            ]);
    }


}