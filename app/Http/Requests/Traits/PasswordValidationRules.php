<?php

namespace App\Http\Requests\Traits;

use App\Rules\NotInPasswordHistory;
use Illuminate\Validation\Rules\Password;

/**
 * Trait PasswordValidationRules
 *
 * Provides reusable validation rules for password fields, including checks
 * against password history to prevent reuse of recent passwords.
 * This trait can be used in FormRequest classes to standardize
 * password validation logic across different scenarios (e.g., login, registration,
 * profile update, or administrative user management).
 *
 * @package App\Traits
 */
trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * This method returns an array of validation rules that are dynamically
     * adjusted based on the HTTP method and the need for password confirmation.
     * It also includes a rule to check if the password has been used recently.
     *
     * @param  bool  $needsConfirmation  Indicates whether the password must be confirmed
     *                                   (e.g., `password_confirmation` field is required).
     * @param  int|null  $userId  The ID of the user for password history validation.
     *                                   Defaults to null, which skips the history check.
     * @return array                     An array of validation rules for the password field.
     */
    protected function passwordRules(bool $needsConfirmation = false, int $userId = null): array
    {
        // Define the base rules for password validation
        $rules = [
            $this->isMethod('post') ? 'required' : 'nullable', // Required for POST requests, optional otherwise
            'string',                        // Must be a string
            Password::min(8)            // Minimum length of 8 characters
            ->max(255)                  // Maximum length of 255 characters
            ->mixedCase()                    // Must include both uppercase and lowercase letters
            ->numbers()                      // Must include at least one numeric character
            ->symbols()                      // Must include at least one special character
            ->uncompromised(),               // Must not appear in common password data breaches
            ...$this->historyRules($userId), // Check against recent passwords
        ];

        // Add the confirmation rule if needed
        if ($needsConfirmation) {
            $rules[] = 'confirmed'; // Ensures the password matches `password_confirmation`
        }

        // Add current_password validation if the request is PUT or PATCH
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules[] = 'current_password';
        }

        return $rules;
    }

    /**
     * Get the validation rules for checking password history.
     *
     * This method provides a validation rule to ensure that the new password
     * has not been used recently by the user. The rule dynamically checks the
     * user's password history if a valid user ID is provided.
     *
     * If the user ID is null, no password history validation is applied.
     *
     * @param  int|null  $userId  The ID of the user whose password history should be validated.
     *                            If null, the history check is skipped.
     * @return array              An array containing the password history validation rule.
     *                            Returns an empty array if no user ID is provided.
     */
    protected function historyRules(?int $userId): array
    {
        return $userId ? [new NotInPasswordHistory($userId)] : [];
    }
}
