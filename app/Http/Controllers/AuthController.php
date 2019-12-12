<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

/**
 * @OA\Info(title="Anime Store API", version="1.0")
 */

class AuthController extends Controller
{

    private $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @OA\Post(
     *     path="api/v1/login",
     *     summary="Log in",
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *     @OA\Property(
     *     property="email",
     *     type="string"
     * ),
     *     @OA\Property(
     *     property="password",
     *     type="string"
     * ),
     *     example={"email": "john@email.com", "password": "thiswillbeencrypted"}
     *     ),
     *     ),
     *     ),
     *     @OA\Response(
     *     response=200,
     *     description="Logged in"
     * ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     * )
     * )
     *
     */

    /**
     * Get a JWT via given credentials.
     * @param $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = array("email" => $request->email, "password" => $request->password);
        if (!$token = $this->auth->attempt($credentials)) {
            return response()->json([
                "errors" => [
                    "code" => "ERROR-6",
                    "title" => "Unauthorized",
                    "message" => "Credenciales inválidas"
                ]
            ], 401);
        }
        return response()->json([
            'token' => $token,
            'expires' => $this->auth->factory()->getTTL() * 60,
        ]);
    }

    /**
     * @OA\Post(
     *     path="api/v1/signup",
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *     mediaType="application/json",
     *     @OA\Schema(
     *     @OA\Property(
     *     property="name",
     *     type="string"
     * ),
     *     @OA\Property(
     *     property="email",
     *     type="string"
     * ),
     *     @OA\Property(
     *     property="password",
     *     type="string"
     * ),
     *     example={"name": "John", "email": "john@email.com", "password": "thiswillbeencrypted"}
     *     ),
     *     ),
     *     ),
     *     @OA\Response(
     *     response=201,
     *     description="User created"
     * ),
     *     @OA\Response(
     *     response=422,
     *     description="Unprocessable Entity"
     * )
     * )
     *
     */

    /**
     * Get a JWT via given credentials.
     *
     * @param CreateUserRequest $request
     * @return UserResource
     */
    public function signup(CreateUserRequest $request)
    {
        $user = new User($request->all());
        $user->password = bcrypt($user->password);
        $user->save();

        return new UserResource($user);
    }

    /**
     * Get the authenticated User.
     *
     * @return UserResource
     */
    public function me()
    {
        return new UserResource($this->auth->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], 200);
    }
}
