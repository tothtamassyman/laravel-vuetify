<?php

namespace App\Http\Requests\Traits;

/**
 * Trait EmailValidationRules
 *
 * This trait provides reusable validation rules for email fields. It dynamically
 * generates rules based on the application's configuration (`validationPolicies.email`)
 * and contextual parameters such as HTTP methods and database table structure.
 *
 * Key Features:
 * - Ensures emails conform to configurable validation standards (e.g., RFC, DNS).
 * - Dynamically determines whether the email must be unique and handles exclusions (ignore ID).
 * - Adapts to the HTTP request method, requiring emails for creation and making them optional for updates.
 * - Supports multi-tenant or administrative systems by dynamically setting the target table.
 *
 * Scenarios:
 * - Login: Basic email validation without uniqueness checks.
 * - Registration: Strict validation, including uniqueness in the `users` table.
 * - Profile Update: Validates format while excluding the current user's email from uniqueness checks.
 *
 * Example Usage:
 * Use this trait in `FormRequest` classes or controllers to standardize email validation logic across different contexts.
 *
 * @package App\Http\Requests\Traits
 */
trait EmailValidationRules
{
    /**
     * Get the validation rules used to validate email addresses.
     *
     * The method dynamically generates validation rules based on the settings
     * in the `config/validationPolicies.php` file.
     *
     * @param  bool  $isUnique  Indicates whether the email must be unique in the database.
     *                              (e.g., `unique:users,email` rule is applied).
     *                              Defaults to true, which enforces uniqueness.
     * @param  int|null  $ignoreId  The ID to ignore for unique validation.
     * @return array                An array of validation rules for email fields.
     */
    protected function emailRules(bool $isUnique = true, int $ignoreId = null): array
    {
        // Get the password validation policies from the configuration file
        $config = config('validationPolicies.email', []);

        $rules = [];

        // Add 'required' or 'nullable' based on HTTP method
        $rules[] = request()->method() === 'POST' ? 'required' : 'nullable';

        // Must be a valid string
        $rules[] = 'string';

        // Add min and max length constraints
        $rules[] = 'min:'.$config['min_length'];
        $rules[] = 'max:'.$config['max_length'];

        // Build email validation standards
        $standards = collect($config['validation_standards'])
            ->filter(fn($enabled) => $enabled)
            ->keys()
            ->implode(',');
        $rules[] = "email:{$standards}";

        // Automatically determine the ignore ID for update requests, if not explicitly provided
        if (is_null($ignoreId) && in_array(request()->method(), ['PUT', 'PATCH'])) {
            $ignoreId = $this->route('user')->id ?? $this->user()->id;
        }

        // Determine the table to apply the unique rule
        // If a specific table is passed via the route, use it; otherwise, default to 'users'
        $table = request()->route('table') ?? 'users';

        // Add uniqueness validation if required
        if ($isUnique) {
            $uniqueRule = "unique:{$table},email";
            if ($ignoreId) {
                $uniqueRule .= ",{$ignoreId}";
            }
            $rules[] = $uniqueRule;
        }

        return $rules;
    }
}
