<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GroupStatsController extends Controller
{
    public function index(): JsonResponse
    {
        $groupStats = UserDetail::where('key', 'current_group_id')
            ->join('groups', 'user_details.value', '=', 'groups.id')
            ->select('groups.name', DB::raw('count(*) as count'))
            ->groupBy('groups.name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->name => $item->count];
            });

        return response()->json([
            'success' => true,
            'data' => $groupStats,
        ]);
    }
}