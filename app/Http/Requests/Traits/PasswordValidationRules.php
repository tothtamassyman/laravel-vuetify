<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rules\Password;

/**
 * Trait PasswordValidationRules
 *
 * Provides reusable validation rules for password fields.
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
     *
     * @param  bool  $needsConfirmation  Indicates whether the password must be confirmed
     *                                   (e.g., `password_confirmation` field is required).
     * @return array                     An array of validation rules for the password field.
     */
    protected function passwordRules(bool $needsConfirmation = false): array
    {
        // Define the base rules for password validation
        $rules = [
            $this->isMethod('post') ? 'required' : 'nullable', // Required for POST requests, optional otherwise
            'string',             // Must be a string
            Password::min(8) // Minimum length of 8 characters
            ->max(255)       // Maximum length of 255 characters
            ->mixedCase()         // Must include both uppercase and lowercase letters
            ->numbers()           // Must include at least one numeric character
            ->symbols()           // Must include at least one special character
            ->uncompromised(),    // Must not appear in common password data breaches
        ];

        // Add the confirmation rule if needed
        if ($needsConfirmation) {
            $rules[] = 'confirmed'; // Ensures the password matches `password_confirmation`
        }

        return $rules;
    }
}
