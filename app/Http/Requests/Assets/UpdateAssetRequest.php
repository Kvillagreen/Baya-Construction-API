<?php

namespace App\Http\Requests\Assets;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_name' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'asset_stocks' => ['sometimes', 'required', 'integer', 'min:0'],
            'model_serial_number' => ['sometimes', 'required', 'string', 'max:255'],
            'asset_acquired_date' => ['nullable', 'date'],
            'asset_price_encrypted' => ['sometimes', 'required', 'string'],
            'assigned_employee_id' => ['nullable', 'string', 'exists:employees,id'],
            'status' => ['sometimes', 'required', Rule::in(['Available', 'Assigned', 'Maintenance'])],
        ];
    }
}
