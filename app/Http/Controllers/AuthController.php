<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AbilitiesController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AbilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest  $request): JsonResponse
    {
        $request->authenticate();

        /** @var User $user */
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken($request->userAgent())->plainTextToken;
        $group = $user->groups()->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => __('messages.group.no_group_associated_with_user'),
            ]);
        }

        $abilities = app(AbilityService::class)->getUserAbilities($user);

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged in',
            'user' => $user,
            'abilities' => $abilities,
            'token' => $token,
            'group_id' => $group->id,
        ]);
    }

    /**
     * Logout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
