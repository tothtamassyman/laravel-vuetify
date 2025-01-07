<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueUserDetail implements ValidationRule
{
    /**
     * The user ID.
     */
    protected ?int $userId;

    /**
     * Create a new rule instance.
     *
     * @param  int  $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table('user_details')
            ->where('user_id', $this->userId)
            ->where('key', $value)
            ->exists();
        if ($exists) {
            $fail(__('auth.user_detail_key_exists', ['attribute' => $value]));
        }
    }
}
