<?php

namespace Jdlx\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Jdlx\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     *
     * @OA\Get(
     *     path="/auth/csfr",
     *     tags={"auth"},
     *     operationId="Set csfr token",
     *     summary="Generate a token for a user on a device",
     *
     *     @OA\Response(
     *          response="204",
     *          description="Return an empty response simply to trigger the storage of the CSRF cookie in the browser."
     *     ),
     *
     *     security={{"bearer_auth": {}}}
     * )
     *
     * @param Request $request
     * @return \Laravel\Sanctum\string|string
     * @throws ValidationException
     */
    public function csfr(Request $request)
    {
        return new Response('', 204);
    }

    /**
     *
     * @OA\Post(
     *     path="/auth/token",
     *     tags={"auth"},
     *     operationId="token",
     *     summary="Generate a token for a user on a device",
     *
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property( property="email", type="string"),
     *              @OA\Property( property="password", type="string"),
     *              @OA\Property( property="device_name", type="string"),
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response="200",
     *          description="Return token to access the API",
     *          @OA\JsonContent(
     *             @OA\Property(
     *              property="data",
     *              type="object",
     *              properties={
     *                  @OA\Property( property="token", type="string" )
     *              }
     *            )
     *         )
     *     ),
     *
     *     security={{"bearer_auth": {}}}
     * )
     *
     * @param Request $request
     * @return \Laravel\Sanctum\string|string
     * @throws ValidationException
     */
    public function token(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        /**
         * @var $user \App\User
         */
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->success(["token" => $user->createToken($request->device_name)->plainTextToken]);
    }

    /**
     *
     * @OA\Get(
     *     path="/auth/user",
     *     tags={"auth"},
     *     operationId="user",
     *     summary="Return currently authenticated user",
     *
     *     @OA\Response(
     *          response="200",
     *          description="Returns a very simple user object",
     *          @OA\JsonContent(
     *              @OA\Property( property="data", ref="#/components/schemas/User" )
     *          )
     *     ),
     *    security={{"bearer_auth": {}}}
     * )
     *
     * @param Request $request
     * @return mixed
     */
    public function user(Request $request)
    {
        $user = Auth::user();
        if($user) {
            return response()->success(new UserResource($user));
        }
        return response()->success();

    }

    /**
     *
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"auth"},
     *     operationId="login",
     *     summary="Login to the application using username and password",
     *
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property( property="email", type="string"),
     *              @OA\Property( property="password", type="string")
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response="200",
     *          description="Return token to access the API",
     *          @OA\JsonContent(
     *              @OA\Property( property="data", ref="#/components/schemas/User" )
     *          )
     *     ),
     *    security={{"bearer_auth": {}}}
     * )
     *
     * @param Request $request
     * @return \Laravel\Sanctum\string|string
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return response()->success(new UserResource(Auth::user()));
        } else {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
    }

    /**
     *
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"auth"},
     *     operationId="logout",
     *     summary="End the session for SPA type authentication",
     *     @OA\Response(
     *          response="200",
     *          description="User has been logged out",
     *          @OA\JsonContent(
     *              @OA\Property( property="data", type="object")
     *          )
     *     ),
     *    security={{"bearer_auth": {}}}
     * )
     *
     * @param Request $request
     * @return \Laravel\Sanctum\string|string
     * @throws ValidationException
     */
    public function logout(Request $request)
    {
        Auth::logout();

        return response()->success([
            "logged_out" => true,
        ], 200);
    }
}
