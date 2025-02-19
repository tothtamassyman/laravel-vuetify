<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
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
            'name' => [
                'required', 'string', 'min:3', 'max:255',
                Rule::unique('permissions')->ignore($this->route('permission'))
            ],
            'description' => ['nullable', 'string', 'min:3', 'max:255'],
            'guard_name' => [
                'required',
                'string',
                'max:255',
                Rule::in(array_keys(config('auth.guards')))
            ],
            'fields' => ['nullable', 'array'],
            'fields.*' => ['required', 'string', 'max:255'],
            'conditions' => ['nullable', 'array'],
            'conditions.*.key' => ['required', 'string', 'max:255'],
            'conditions.*.operator' => ['required', 'string', 'max:10'],
            'conditions.*.value' => ['required', 'string', 'max:255'],
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
            'name' => __('validation.attributes.permissions.name'),
            'description' => __('validation.attributes.permissions.description'),
            'guard_name' => __('validation.attributes.permissions.guard_name'),
            'fields' => __('validation.attributes.permissions.fields'),
            'fields.*' => __('validation.attributes.permissions.fields.field'),
            'conditions' => __('validation.attributes.permissions.conditions'),
            'conditions.*.key' => __('validation.attributes.permissions.conditions.key'),
            'conditions.*.operator' => __('validation.attributes.permissions.conditions.operator'),
            'conditions.*.value' => __('validation.attributes.permissions.conditions.value'),
            'condition' => __('validation.attributes.permissions.condition'),
        ];
    }
}
