<?php

namespace App\Http\Requests\Traits;

use App\Rules\NotInPasswordHistory;
use Illuminate\Validation\Rules\Password;

/**
 * Trait PasswordValidationRules
 *
 * This trait provides reusable validation rules for password fields. It dynamically
 * generates rules based on the application's configuration (`validationPolicies.password`)
 * and contextual parameters such as HTTP methods and user-specific password history.
 *
 * Key Features:
 * - Enforces configurable password policies, including length, complexity, and uncompromised checks.
 * - Dynamically adapts to HTTP request methods (e.g., `required` for creation, `nullable` for updates).
 * - Integrates password history validation to prevent users from reusing recent passwords.
 * - Supports optional password confirmation for sensitive operations (e.g., updates).
 *
 * Scenarios:
 * - Registration: Enforces strong password requirements for new users.
 * - Password Updates: Validates password history and requires confirmation.
 * - Login: Password validation is skipped as it is verified against the database.
 *
 * Example Usage:
 * Use this trait in `FormRequest` classes or controllers to standardize password validation logic
 * and ensure consistent security policies across the application.
 *
 * @package App\Http\Requests\Traits
 */
trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * The method dynamically generates validation rules based on the settings
     * in the `config/validationPolicies.php` file.
     *
     * @param  bool  $needsConfirmation  Indicates whether the password must be confirmed
     *                                   (e.g., `password_confirmation` field is required).
     * @param  int|null  $userId  The ID of the user for password history validation.
     *                                   Defaults to null, which skips the history check.
     * @return array                     An array of validation rules for the password field.
     */
    protected function passwordRules(bool $needsConfirmation = false, int $userId = null): array
    {
        // Get the password validation policies from the configuration file
        $config = config('validationPolicies.password');

        $rules = [];

        // Add 'required' or 'nullable' based on HTTP method
        $rules[] = request()->method() === 'POST' ? 'required' : 'nullable';

        // Must be a valid string
        $rules[] = 'string';

        // Ensure min_length and max_length are sensible
        $minLength = max(8, $config['min_length']);
        $maxLength = min(255, max($minLength, $config['max_length']));

        // Initialize Password rule
        $passwordRule = Password::min($minLength)
            ->max($maxLength);

        // Add mixed case if enabled, otherwise add letters
        if ($config['mixed_case']) {
            $passwordRule->mixedCase();
        } else {
            if ($config['letters']) {
                $passwordRule->letters();
            }
        }

        // Add numbers if enabled
        if ($config['numbers']) {
            $passwordRule->numbers();
        }

        // Add symbols if enabled
        if ($config['symbols']) {
            $passwordRule->symbols();
        }

        // Add uncompromised check if enabled
        if ($config['uncompromised']) {
            $passwordRule->uncompromised();
        }

        // Add the base password rule
        $rules[] = $passwordRule;

        // Add history check if enabled
        $rules = array_merge($rules, $this->historyRules($userId));

        // Add confirmation rule if explicitly needed
        if ($needsConfirmation) {
            $rules[] = 'confirmed';
        }

        // Add current_password validation dynamically
        $rules = array_merge($rules, $this->currentPasswordRule());

        return $rules;
    }

    /**
     * Adds validation rules to check the user's password history.
     * This ensures that users cannot reuse recently used passwords,
     * enhancing security by mitigating predictable password cycling.
     *
     * @param  int|null  $userId  The ID of the user whose password history should be validated.
     *                            If null, the history check is skipped.
     * @return array              An array containing the password history validation rule.
     *                            Returns an empty array if no user ID is provided or history check is disabled.
     */
    protected function historyRules(?int $userId): array
    {
        $config = config('validationPolicies.password');
        if (!$config['history_check'] || !$userId) {
            return [];
        }
        return [new NotInPasswordHistory($userId)];
    }

    /**
     * Adds validation rule to check the user's current password.
     * This ensures that users must provide their current password during updates,
     * enhancing security for sensitive operations.
     *
     * @return array An array containing the current password validation rule.
     *               Returns an empty array if the rule does not apply.
     */
    protected function currentPasswordRule(): array
    {
        return in_array(request()->method(), ['PUT', 'PATCH']) && request()->filled('password')
            ? ['current_password']
            : [];
    }
}