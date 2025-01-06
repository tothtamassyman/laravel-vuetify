<?php

namespace App\Rules;

use App\Models\PasswordHistory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Rule to prevent reuse of recent passwords.
 *
 * This rule checks if the provided password matches any of the user's
 * recently used passwords stored in the password history table. The
 * number of passwords checked is configurable through the `auth.php`
 * configuration file (`password_history_limit`).
 */
class NotInPasswordHistory implements ValidationRule
{
    /**
     * The user ID to check the password history against.
     *
     * @var int
     */
    protected int $userId;

    /**
     * Create a new rule instance.
     *
     * @param  int  $userId  The ID of the user whose password history will be checked.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Run the validation rule.
     *
     * This rule checks if the provided password matches any of the user's
     * recently used passwords. The limit for password history is retrieved
     * from the `auth.password_history_limit` configuration value.
     *
     * @param  string  $attribute  The name of the attribute being validated.
     * @param  mixed  $value  The value of the attribute being validated (plain text password).
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail  A callback to trigger validation failure.
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Get the password history limit from the configuration
        $historyLimit = config('auth.password_history_limit');

        // Get the last N passwords for the user
        $recentPasswords = PasswordHistory::where('user_id', $this->userId)
            ->latest('created_at')
            ->take($historyLimit)
            ->pluck('password');

        // Check if the provided password matches any of the recent passwords
        if ($recentPasswords->contains(fn($hashedPassword) => Hash::check($value, $hashedPassword))) {
            $fail(__('validation.password.history', ['limit' => $historyLimit]));
        }
    }
}
