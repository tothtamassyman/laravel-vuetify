<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Traits\EmailValidationRules;
use App\Http\Requests\Traits\PasswordValidationRules;
use App\Rules\UniqueUserDetail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    // Use the EmailValidationRules and PasswordValidationRules traits to include the emailRules() and passwordRules() methods
    use EmailValidationRules, PasswordValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => $this->emailRules(true, $this->getUserId()),
            'password' => $this->passwordRules(),
            'details' => ['nullable', 'array'],
            'details.*.key' => ['required', 'string', 'max:255'],
            'details.*.value' => ['nullable', 'string', 'max:255', new UniqueUserDetail($this->getUserId())],
        ];
    }

    /**
     * Get the user ID from the request or the authenticated user.
     */
    public function getUserId(): int
    {
        return $this->user->id ?? auth()->id();
    }
}
