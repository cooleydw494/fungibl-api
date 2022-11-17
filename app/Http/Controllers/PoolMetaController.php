<?php

namespace App\Http\Controllers;

use App\Models\PoolMeta;
use App\Models\PoolMetaLog;
use Carbon\Carbon;
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
        return response()->json(['pool_metas' => PoolMeta::get()]);
    }

    /**
     * Get all PoolMetaLogs for a specified duration (up to current)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLogs(Request $request): JsonResponse
    {
        $start = Carbon::now();
        $start = match ($request->input('duration')) {
            '1d' => $start->subDay(),
            '7d' => $start->subDays(7),
            '30d' => $start->subDays(30),
            default => $start->subHour(),
        };
        $logs = PoolMetaLog::where('created_at', '>=', $start)
                               ->get();
        return response()->json(['success' => 'logs fetched', 'logs' => $logs]);
    }

    /**
     * Get any PoolMetaLogs created after the specified last_log_id
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLatestLogs(Request $request): JsonResponse
    {
        $lastLogId = $request->input('last_log_id');
        $logs = PoolMetaLog::where('id', '>', $lastLogId)
                           ->get();
        return response()->json([
            'success' => 'latest logs fetched',
            'latest_logs' => $logs,
        ]);
    }
}
