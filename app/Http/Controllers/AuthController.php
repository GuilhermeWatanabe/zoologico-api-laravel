<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Janitor;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if ($token = auth('api-janitors')->attempt($credentials)) {
            return $this->respondWithToken($token, 'api-janitors');
        }
        if ($token = auth('api-animals')->attempt($credentials)) {
            return $this->respondWithToken($token, 'api-animals');
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        //variables created to compare what user is logout
        $janitor = new Janitor();
        $animal = new Animal();

        if (auth('api-janitors')->user() instanceof $janitor) {
            auth('api-janitors')->logout();
        }

        if (auth('api-animals')->user() instanceof $animal) {
            auth('api-animals')->logout();
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param  string $guard
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $guard)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth($guard)->factory()->getTTL() * 60
        ]);
    }
}
