<?php

namespace Tests\Feature;

use App\Comment;
use App\Serie;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_comments_of_one_serie()
    {
       $serieWithComments = Factory(Serie::class)->create();
       $user = factory(User::class)->create();
       $serieWithComments->users()->attach($user);

       $comment = factory(Comment::class)
            ->create([
                    "serie_id" => $serieWithComments->id,
                    "user_id" =>  $user->id
            ]);
       $serieWithComments->comments()->save($comment);

       $response = $this->get('api/v1/series/'. $serieWithComments->id. '/comments' );

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }


}