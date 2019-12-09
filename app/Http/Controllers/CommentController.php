<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ListCommentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Comment::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Http\Resources\CommentResource
     */
    public function store(CreateCommentRequest $request)
    {
        $token = $request->bearerToken();

        $id_serie = $request->route('id');

        $user = DB::table('users')->where('api_token', '=', $token)->first();

        $serie_user_exists = DB::table('serie_user')
            ->where([
                ['serie_id', '=', $id_serie],
                ['user_id', '=', $user->id]
            ])
            ->exists();

        if ($serie_user_exists) {
            $comment_id = DB::table('comments')->insertGetId([
                'description' => $request->get('description'),
                'user_id' => $user->id,
                'serie_id' => $id_serie
            ]);

            $comment = Comment::findOrFail($comment_id);

            return new CommentResource($comment);

        } else {
            return response()->json([
                "errors" => [
                    "code" => "ERROR-5",
                    "title" => "Serie Not Purchased",
                    "message" => "No posee esta serie en su inventario"
                ]
            ], 401);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \App\Http\Resources\CommentResource
     */
    public function show($id)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function list($id)
    {
        $comments = Comment::where('serie_id', $id)->get();

        $comment = ListCommentResource::collection($comments);
        return $comment;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
