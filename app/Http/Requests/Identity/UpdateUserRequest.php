<?php

namespace App\Http\Requests\Identity;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->getKey() : (string) $user;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['sometimes', 'string', 'min:6'],
            'user_type' => ['sometimes', Rule::in(['Admin', 'User'])],
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
