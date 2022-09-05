<?php

namespace App\Http\Controllers;

use App\Models\Nonce;
use App\Models\User;
use App\Rules\AlgorandAddressRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

/**
 * Custom jwt-web3 based auth: https://github.com/tymondesigns/jwt-auth/issues/1551
 * https://laravel.com/docs/9.x/authentication#adding-custom-user-providers
 */
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['requestChallenge', 'login']]);
    }

    /**
     * Requests a challenge for the given Algorand address.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function requestChallenge(Request $request, string $address): JsonResponse
    {
        // Validate address
        info($address);
        info($request->route('address'));
        $request->merge(['address' => $request->route('address')]);
        $request->validate([
            'address' => [
                'required',
                new AlgorandAddressRule
            ]
        ]);

        // Check if address exists, if it exists, fetch nonce
        $user = User::firstOrCreate(
            ['algorand_address' => $address],
            ['algorand_address' => $address, 'nonce' => Nonce::create()]
        );

        return response()->json([
            'address' => $address,
            'challenge' => $user->nonce,
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function login(): JsonResponse
    {
        $credentials = request(['algorand_address', 'signed_tx']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Create a new challenge for the user.
        auth()->user()->nonce = Nonce::create();
        //auth()->user()->save();

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function whoami(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
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
    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
