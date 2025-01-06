<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Traits\EmailValidationRules;
use App\Http\Requests\Traits\PasswordValidationRules;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    // Use the EmailValidationRules trait to include the emailRules() method
    use EmailValidationRules;

    // Use the PasswordValidationRules trait to include the passwordRules() method
    use PasswordValidationRules;

    /**
     * Handle an incoming new password request.
     *
     * Validates the request, resets the user's password, and redirects the user
     * to the login page if successful. Throws a validation exception if the process fails.
     *
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the request using the Email and Password validation rules
        $request->validate([
            'token' => 'required',
            // No uniqueness check for password reset
            'email' => $this->emailRules(false),
            // Requires confirmation and history check
            'password' => $this->passwordRules(true, $this->getUserIdByEmail($request->email)),
        ]);

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

                // Add password to password history
                $user->passwordHistories()->create([
                    'password' => $request->password,
                ]);

                // Trigger the PasswordReset event
                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will return a JSON response
        // with a success message. If there is an error, we will throw a validation
        // exception with an appropriate error message.
        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __($status)
            ]);
        }

        // Throw a validation exception with the appropriate error message
        throw ValidationException::withMessages([
            'email' => [trans($status)],
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
