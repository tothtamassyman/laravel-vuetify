<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Traits\EmailValidationRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    // Use the EmailValidationRules trait to include the emailRules() method
    use EmailValidationRules;

    /**
     * Handle an incoming password reset link request.
     *
     * Validates the provided email address and attempts to send a password
     * reset link. If successful, the user is redirected back with a success
     * message. If unsuccessful, a validation exception is thrown.
     *
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the email address using the emailRules() method from the trait
        $request->validate([
            'email' => $this->emailRules(false), // No uniqueness check needed for password reset
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Redirect back with a success message if the link was sent successfully
        if ($status == Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => __($status)
            ]);
        }

        // Throw a validation exception with the appropriate error message if the link could not be sent
        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
