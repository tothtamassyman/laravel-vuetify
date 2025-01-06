<?php

namespace App\Http\Requests\Traits;

/**
 * Trait EmailValidationRules
 *
 * Provides reusable validation rules for email fields.
 * This trait can be used in FormRequest classes to standardize
 * email validation logic across different scenarios (e.g., login, registration,
 * profile update, or administrative user management).
 *
 * @package App\Traits
 */
trait EmailValidationRules
{
    /**
     * Get the validation rules used to validate emails.
     *
     * This method returns an array of validation rules that are dynamically
     * adjusted based on the HTTP method, uniqueness requirements,
     * and optional ignore ID.
     *
     * @param  bool  $isUnique  Indicates whether the email must be unique in the database.
     *                          Defaults to true for scenarios like registration or user updates.
     * @param  int|null  $ignoreId  An optional user ID to exclude from the uniqueness check.
     *                              If not explicitly provided, the method attempts to resolve it
     *                              automatically for non-POST requests (e.g., updates).
     * @return string[]  An array of validation rules for the email field.
     */
    protected function emailRules(bool $isUnique = true, int $ignoreId = null): array
    {
        // Automatically determine the ignore ID for non-POST requests, if not explicitly provided
        if (is_null($ignoreId) && !$this->isMethod('post')) {
            $ignoreId = $this->route('user')->id ?? $this->user()->id;
        }

        // Determine the table to apply the unique rule
        // If a specific table is passed via the route, use it; otherwise, default to 'users'
        $table = $this->route('table') ?? 'users';

        // Base validation rules for email fields
        $rules = [
            $this->isMethod('post') ? 'required' : 'nullable', // Required for POST requests, optional otherwise
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
