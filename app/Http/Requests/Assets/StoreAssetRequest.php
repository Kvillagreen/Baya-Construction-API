<?php

namespace App\Http\Requests\Assets;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'asset_stocks' => ['required', 'integer', 'min:0'],
            'model_serial_number' => ['required', 'string', 'max:255'],
            'asset_acquired_date' => ['nullable', 'date'],
            'asset_price_encrypted' => ['required', 'string'],
            'assigned_employee_id' => ['nullable', 'string', 'exists:employees,id'],
            'status' => ['required', Rule::in(['Available', 'Assigned', 'Maintenance'])],
        ];
    }
}
