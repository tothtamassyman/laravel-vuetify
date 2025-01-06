<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Traits\PasswordValidationRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class PasswordController
 *
 * Handles the updating of a user's password.
 *
 * @package App\Http\Controllers\Auth
 */
class PasswordController extends Controller
{
    // Use the PasswordValidationRules trait to include the passwordRules() method
    use PasswordValidationRules;

    /**
     * Handle an incoming password update request.
     *
     * Validates the request, updates the user's password, and returns a JSON response.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        // Validate the request using the trait's passwordRules method
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => $this->passwordRules(true, $request->user()->id),
        ]);

        // Update the user's password
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Add password to password history
        $request->user()->passwordHistories()->create([
            'password' => $request->password,
        ]);

        return response()->json([
            'message' => __('passwords.updated')
        ]);
    }
}
