<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Traits\PasswordValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePasswordRequest
 *
 * Handles the validation of password updates for authenticated users.
 * The PasswordValidationRules trait dynamically adds the current_password
 * validation rule for PUT or PATCH requests. This ensures that the user
 * must provide their current password before updating to a new one.
 *
 * @package App\Http\Requests\Auth
 */
class UpdatePasswordRequest extends FormRequest
{
    // Use the PasswordValidationRules trait to include the passwordRules() method
    use PasswordValidationRules;

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
            'password' => $this->passwordRules(true, $this->user()->id),
        ];
    }
}
