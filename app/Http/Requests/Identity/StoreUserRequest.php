<?php

namespace App\Http\Requests\Identity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'user_type' => ['required', Rule::in(['Admin', 'User'])],
            'is_active' => ['sometimes', 'boolean'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['string', 'exists:roles,id'],
            'endpoint_overrides' => ['nullable', 'array'],
            'endpoint_overrides.*.endpoint' => ['required_with:endpoint_overrides', 'string'],
            'endpoint_overrides.*.method' => ['required_with:endpoint_overrides', Rule::in(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])],
            'endpoint_overrides.*.allowed' => ['required_with:endpoint_overrides', 'boolean'],
        ];
    }
}
