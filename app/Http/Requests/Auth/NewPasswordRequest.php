<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Traits\EmailValidationRules;
use App\Http\Requests\Traits\PasswordValidationRules;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class NewPasswordRequest
 *
 * Ensures the password meets security standards and has not been used recently.
 * The PasswordValidationRules trait automatically handles the password history
 * validation to prevent users from reusing old passwords.
 *
 * @package App\Http\Requests\Auth
 */
class NewPasswordRequest extends FormRequest
{
    // Use the EmailValidationRules and PasswordValidationRules traits to include the emailRules() and passwordRules() methods
    use EmailValidationRules, PasswordValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userId = $this->getUserIdByEmail($this->input('email'));

        return [
            'token' => 'required',
            'email' => $this->emailRules(false), // No uniqueness check for password reset
            'password' => $this->passwordRules(true, $userId), // Requires confirmation and history check
        ];
    }

    /**
     * Get the user ID by email for password history validation.
     *
     * @param  string  $email
     * @return int|null
     */
    protected function getUserIdByEmail(string $email): ?int
    {
        return User::where('email', $email)->value('id');
    }
}
