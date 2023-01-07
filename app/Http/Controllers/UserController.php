<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNfdInfo(Request $request): JsonResponse
    {
        $nfd = $request->get('nfd');
        $avatarUrl = $request->get('avatar_url');
        if (is_null($nfd) || is_null($avatarUrl)) {
            return response()->json(['error' => 'invalid input'], 409);
        }
        $user = \Auth::user();
        $user->update([
            'nfd' => $request->get('nfd'),
            'avatar_url' => $request->get('avatar_url'),
        ]);

        return response()->json([
            'success' => 'updated NFD info',
            'user' => $user,
        ]);
    }
}
