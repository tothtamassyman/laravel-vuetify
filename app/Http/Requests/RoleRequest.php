<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
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
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $this->route('id')],
            'description' => ['nullable', 'string', 'min:3', 'max:255'],
            'guard_name' => [
                'required',
                'string',
                'max:255',
                Rule::in(array_keys(config('auth.guards')))
            ],
            'permissions' => ['required', 'array'],
            'condition' => 'nullable|string|in:and,or',
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
            'name' => __('validation.attributes.roles.name'),
            'description' => __('validation.attributes.roles.description'),
            'guard_name' => __('validation.attributes.roles.guard_name'),
            'permissions' => __('validation.attributes.roles.permissions'),
        ];
    }
}
