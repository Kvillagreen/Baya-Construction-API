<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class StoreOvertimeLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logs' => ['required', 'array', 'min:1'],
            'logs.*.employee_id' => ['required', 'exists:employees,id'],
            'logs.*.date' => ['required', 'date'],
            'logs.*.hours' => ['required', 'numeric', 'min:0.5'],
            'logs.*.approved' => ['required', 'boolean'],
            'logs.*.reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
