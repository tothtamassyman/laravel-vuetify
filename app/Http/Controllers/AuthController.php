<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AbilityService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Login
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
{
    $request->authenticate();

    /** @var User $user */
    $user = $request->user();
    $user->tokens()->delete();
    $token = $user->createToken($request->userAgent())->plainTextToken;

    try {
        $groupId = $user->default_group_id;
        $user->setDetail('current_group_id', $groupId);
        app(PermissionRegistrar::class)->setPermissionsTeamId($groupId);
        $abilities = app(AbilityService::class)->getUserAbilities($user);

        return response()->json([
            'success' => true,
            'message' => __('messages.auth.login_successful'),
            'user' => $user->load('groups'),
            'abilities' => $abilities,
            'token' => $token,
            'group_id' => $groupId,
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => __('messages.auth.login_failed'),
        ], 403);
    }
}

    /**
     * Logout
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->deleteDetail('current_group_id');
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.auth.logout_successful'),
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
