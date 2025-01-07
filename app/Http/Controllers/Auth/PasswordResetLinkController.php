<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * Class PasswordResetLinkController
 *
 * Handles the process of sending a password reset link to the user.
 *
 * @package App\Http\Controllers\Auth
 */
class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * Validates the email address using the ForgotPasswordRequest and attempts
     * to send a password reset link. If the link is sent successfully, a JSON
     * response with a success message is returned. If the process fails,
     * a ValidationException is thrown.
     *
     * The ValidationException ensures:
     * 1. The user receives clear feedback about why the request failed (e.g., invalid email).
     * 2. Sensitive information (e.g., whether the email exists) is protected, enhancing security.
     * 3. The frontend can handle the error consistently, adhering to Laravel's standard
     *    validation error format.
     *
     * @param  ForgotPasswordRequest  $request  The validated password reset link request.
     * @return JsonResponse                     A JSON response indicating success or failure.
     *
     * @throws ValidationException              Thrown if the password reset process fails.
     */
    public function store(ForgotPasswordRequest $request): JsonResponse
    {
        // Send the password reset link to the specified email address
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // If the reset link was sent successfully, return a success message
        if ($status == Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => __($status),
            ]);
        }

        // Throwing a ValidationException ensures secure and structured error handling
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
