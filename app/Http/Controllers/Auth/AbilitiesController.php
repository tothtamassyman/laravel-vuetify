<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AbilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AbilitiesController extends Controller
{

    /**
     * Get abilities for the authenticated user.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $abilities = [];

        $user = auth('api')->user();

        if ($user !== null) {
            $abilities = app(AbilityService::class)->getUserAbilities($user);
        }

        return response()->json([
            'abilities' => $abilities,
        ]);
    }
}
