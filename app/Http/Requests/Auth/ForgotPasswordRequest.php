<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Traits\EmailValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ForgotPasswordRequest
 *
 * Validates the email address provided for the password reset process.
 * The emailRules method ensures that the email is valid, properly formatted,
 * and not unique, since existing users need to reset their passwords.
 *
 * This request is typically used in the PasswordResetLinkController to
 * validate the incoming email address before sending a reset link.
 *
 * @package App\Http\Requests\Auth
 */
class ForgotPasswordRequest extends FormRequest
{
    // Use the EmailValidationRules trait to include the emailRules() method
    use EmailValidationRules;

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
        return [
            'email' => $this->emailRules(false),
        ];
    }
}
