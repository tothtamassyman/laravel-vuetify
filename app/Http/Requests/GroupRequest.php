<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupRequest extends FormRequest
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
                Rule::unique('groups')->ignore($this->route('group'))
            ],
            'description' => ['nullable', 'string', 'min:3', 'max:255'],
            'users' => 'nullable|array',
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
            'name' => __('validation.attributes.groups.name'),
            'description' => __('validation.attributes.groups.description'),
            'users' => __('validation.attributes.groups.users'),
            'condition' => __('validation.attributes.groups.condition'),
        ];
    }
}
