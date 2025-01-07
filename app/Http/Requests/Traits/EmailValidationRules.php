<?php

namespace App\Http\Requests\Traits;

/**
 * Trait EmailValidationRules
 *
 * Provides reusable validation rules for email fields.
 * This trait can be used in FormRequest classes or controllers to standardize
 * email validation logic across different scenarios (e.g., login, registration,
 * profile update, or administrative user management).
 *
 * @package App\Traits
 */
trait EmailValidationRules
{
    /**
     * Dynamically determines the database table to use for validation.
     * The route('table') allows for flexibility in multi-tenant or
     * administrative systems where different tables may be used for different
     * user types (e.g., `admins`, `customers`).
     *
     * @param  bool  $isUnique  Indicates whether the email must be unique in the database.
     * @param  int|null  $ignoreId  The ID to ignore for unique validation.
     * @return array  An array of validation rules for email fields.
     */
    protected function emailRules(bool $isUnique = true, int $ignoreId = null): array
    {
        // Automatically determine the ignore ID for non-POST requests, if not explicitly provided
        if (is_null($ignoreId) && request()->method() !== 'POST') {
            $ignoreId = $this->route('user')->id ?? $this->user()->id;
        }

        // Determine the table to apply the unique rule
        // If a specific table is passed via the route, use it; otherwise, default to 'users'
        $table = request()->route('table') ?? 'users';

        // Base validation rules for email fields
        $rules = [
            request()->method() === 'POST' ? 'required' : 'nullable', // Required for POST requests, optional otherwise
            'string',                     // Must be a valid string
            'email:rfc,dns,spoof,filter', // Must conform to various email validation standards
            'min:6',                      // Minimum length of 6 characters
            'max:255',                    // Maximum length of 255 characters
        ];

        // Add uniqueness validation if required
        if ($isUnique) {
            $uniqueRule = "unique:{$table},email"; // Ensure uniqueness in the specified table
            if ($ignoreId) {
                $uniqueRule .= ','.$ignoreId;   // Exclude the specified ID from the uniqueness check
            }
            $rules[] = $uniqueRule;
        }

        return $rules;
    }
}
