<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

/**
 * Class PasswordController
 *
 * Handles the updating of a user's password, including validation,
 * updating the password, and managing the password history. The password
 * history ensures that users cannot reuse recently used passwords, which
 * improves security by preventing predictable password cycling.
 *
 * @package App\Http\Controllers\Auth
 */
class PasswordController extends Controller
{
    /**
     * Handle an incoming password update request.
     *
     * Validates the request using the UpdatePasswordRequest, updates the user's password,
     * and adds the new password to the user's password history. Returns a JSON response
     * indicating success or failure.
     *
     * @param  UpdatePasswordRequest  $request
     * @return JsonResponse
     */
    public function update(UpdatePasswordRequest $request): JsonResponse
    {
        // Update the user's password
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // Add the new password to the user's password history
        $request->user()->passwordHistories()->create([
            'password' => $request->password,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('passwords.updated'),
        ]);
    }
}
