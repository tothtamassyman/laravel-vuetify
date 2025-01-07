<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Class NewPasswordController
 *
 * Handles the resetting of a user's password.
 *
 * @package App\Http\Controllers\Auth
 */
class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * This method validates the request, attempts to reset the user's password,
     * updates the password history, and triggers the necessary events. If the
     * reset is successful, a JSON response with a success message is returned.
     * If the reset fails, a validation exception is thrown with the appropriate error message.
     *
     * @param  NewPasswordRequest  $request  The incoming password reset request.
     * @return JsonResponse                  A JSON response indicating success or failure.
     *
     * @throws ValidationException           Thrown if the password reset process fails.
     */
    public function store(NewPasswordRequest $request): JsonResponse
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                // Update the user's password and remember token
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Add the new password to the user's password history
                $user->passwordHistories()->create([
                    'password' => $request->password,
                ]);

                /**
                 * Triggers the PasswordReset event after a successful password reset.
                 * This event can be used to perform additional actions, such as logging
                 * the reset attempt, notifying administrators, or sending a confirmation
                 * email to the user.
                 */
                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will return a JSON response
        // with a success message. If there is an error, we will throw a validation
        // exception with an appropriate error message.
        if ($status == Password::PASSWORD_RESET) {
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

    /**
     * Retrieve the user ID by email address.
     *
     * This method fetches the user ID based on the provided email address
     * to use for password history validation.
     *
     * @param  string  $email
     * @return int|null
     */
    protected function getUserIdByEmail(string $email): ?int
    {
        return User::where('email', $email)->value('id');
    }
}
