<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Validation Policy Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the validation rules for passwords. These rules
    | ensure that passwords meet the application's security requirements, such as
    | minimum and maximum length, complexity, and history checks. Default values
    | are provided for strong password requirements but can be customized using
    | environment variables to suit specific needs.
    |
    | Configuration Details:
    |
    | - `min_length`: Defines the minimum number of characters required for a password.
    |   Default: 8
    |
    | - `max_length`: Sets the maximum number of characters allowed for a password.
    |   Default: 255
    |
    | - `mixed_case`: Requires passwords to include both uppercase and lowercase letters.
    |   Default: true
    |
    | - `numbers`: Requires passwords to contain at least one numeric character.
    |   Default: true
    |
    | - `symbols`: Requires passwords to contain at least one special character.
    |   Default: true
    |
    | - `letters`: Ensures passwords include at least one letter (regardless of case).
    |   Default: false
    |
    | - `uncompromised`: Validates that the password has not appeared in known data breaches.
    |   This check is performed using Laravel's uncompromised password validation.
    |   Default: true
    |
    | - `history_check`: Enforces password history policies to prevent users from
    |   reusing recently used passwords.
    |   Default: true
    |
    */
    "password" => [
        // Minimum password length (default: 8)
        'min_length' => (int) env('PASSWORD_POLICIES_MIN_LENGTH', 8),

        // Maximum password length (default: 255)
        'max_length' => (int) env('PASSWORD_POLICIES_MAX_LENGTH', 255),

        // Enforce mixed case (upper and lowercase letters) (default: true)
        'mixed_case' => filter_var(env('PASSWORD_POLICIES_MIXED_CASE', true), FILTER_VALIDATE_BOOLEAN),

        // Require at least one number (default: true)
        'numbers' => filter_var(env('PASSWORD_POLICIES_NUMBERS', true), FILTER_VALIDATE_BOOLEAN),

        // Require at least one special character (default: true)
        'symbols' => filter_var(env('PASSWORD_POLICIES_SYMBOLS', true), FILTER_VALIDATE_BOOLEAN),

        // Require at least one letter (default: false)
        'letters' => filter_var(env('PASSWORD_POLICIES_LETTERS', false), FILTER_VALIDATE_BOOLEAN),

        // Avoid passwords found in common data breaches (default: true)
        'uncompromised' => filter_var(env('PASSWORD_POLICIES_UNCOMPROMISED', true), FILTER_VALIDATE_BOOLEAN),

        // Enforce password history check to prevent reuse of recent passwords (default: true)
        'history_check' => filter_var(env('PASSWORD_POLICIES_HISTORY_CHECK', true), FILTER_VALIDATE_BOOLEAN),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Validation Policy Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the validation rules for email addresses.
    | These rules are used dynamically across the application to ensure that
    | email inputs conform to the desired format and security standards. The
    | default values provided here enforce basic email validation but can be
    | overridden using environment variables to fit the application's needs.
    |
    | Configuration Details:
    |
    | - `min_length`: Defines the minimum number of characters an email address must have.
    |   Default: 6
    |
    | - `max_length`: Sets the maximum number of characters allowed for an email address.
    |   Default: 255
    |
    | - `validation_standards`: Specifies the validation checks to be applied to email addresses.
    |   Each standard can be toggled independently:
    |     - `rfc`: Ensures compliance with RFC 5322 standards.
    |       Default: true
    |     - `strict`: Applies strict validation, excluding warnings from RFC compliance.
    |       Default: false
    |     - `dns`: Validates that the domain of the email address has valid DNS records.
    |       Default: true
    |     - `spoof`: Checks for spoofed email addresses to prevent impersonation.
    |       Default: true
    |     - `filter`: Uses PHP's `FILTER_VALIDATE_EMAIL` for basic format validation.
    |       Default: true
    |     - `filter_unicode`: Enables Unicode-aware email validation to support non-ASCII characters.
    |       Default: false
    |
    */
    "email" => [
        // Minimum email length (default: 6)
        'min_length' => (int) env('EMAIL_POLICIES_MIN_LENGTH', 6),
        // Maximum email length (default: 255)
        'max_length' => (int) env('EMAIL_POLICIES_MAX_LENGTH', 255),

        // Email address validation standards
        'validation_standards' => [
            // RFC 5322 validation
            'rfc' => filter_var(env('EMAIL_POLICIES_VALIDATION_STANDARDS_RFC', true), FILTER_VALIDATE_BOOLEAN),
            // Strict validation (NoRFCWarningsValidation)
            'strict' => filter_var(env('EMAIL_POLICIES_VALIDATION_STANDARDS_STRICT', false), FILTER_VALIDATE_BOOLEAN),
            // DNS validation
            'dns' => filter_var(env('EMAIL_POLICIES_VALIDATION_STANDARDS_DNS', true), FILTER_VALIDATE_BOOLEAN),
            // Spoofing prevention
            'spoof' => filter_var(env('EMAIL_POLICIES_VALIDATION_STANDARDS_SPOOF', true), FILTER_VALIDATE_BOOLEAN),
            // Filter validation
            'filter' => filter_var(env('EMAIL_POLICIES_VALIDATION_STANDARDS_FILTER', true), FILTER_VALIDATE_BOOLEAN),
            // Unicode-aware filter validation
            'filter_unicode' => filter_var(env('EMAIL_POLICIES_VALIDATION_STANDARDS_FILTER_UNICODE', false), FILTER_VALIDATE_BOOLEAN),
        ],
    ],
];
