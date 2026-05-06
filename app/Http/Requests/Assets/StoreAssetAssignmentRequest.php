<?php

namespace App\Http\Requests\Assets;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetAssignmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'assigned_to' => ['nullable', 'exists:users,id'],
            'released_by' => ['nullable', 'exists:users,id'],
            'assigned_date' => ['nullable', 'date'],
            'expected_return_date' => ['nullable', 'date'],
            'returned_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'performed_by' => ['nullable', 'exists:users,id'],
        ];
    }
}
