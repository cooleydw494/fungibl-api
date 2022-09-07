<?php

namespace App\Http\Controllers;

use App\Models\PoolMeta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PoolMetaController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(['metas' => PoolMeta::get()]);
    }
}
