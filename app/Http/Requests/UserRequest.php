<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\EmailValidationRules;
use App\Http\Requests\Traits\PasswordValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    use EmailValidationRules;
    use PasswordValidationRules;

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
            'name' => 'required|string|min:3|max:255',
            'email' => $this->emailRules(true),
            'password' => $this->passwordRules(),
            'groups' => 'required|array',
            'groups.id' => 'required|integer|exists:groups,id',
            'roles' => 'nullable|array',
            'roles.id' => 'required|integer|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.id' => 'required|integer|exists:permissions,id',
            'details' => 'nullable|array',
            'details.default_group_id' => 'required|integer|exists:groups,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'email' => __('validation.attributes.email'),
            'password' => __('validation.attributes.password'),
            'groups' => __('validation.attributes.users.groups'),
            'groups.id' => __('validation.attributes.users.groups.id'),
            'roles' => __('validation.attributes.users.roles'),
            'roles.id' => __('validation.attributes.users.roles.id'),
            'permissions' => __('validation.attributes.users.permissions'),
            'permissions.id' => __('validation.attributes.users.permissions.id'),
            'details' => __('validation.attributes.users.details'),
            'details.default_group_id' => __('validation.attributes.users.details.default_group_id'),
        ];
    }

//    public function passedValidation(): void
//    {
//        $this->user()->setDetail('default_group_id', $this->input('default_group_id'));
//    }
}