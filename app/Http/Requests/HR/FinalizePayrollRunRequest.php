<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class FinalizePayrollRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payroll_period_id' => ['required', 'exists:payroll_periods,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
        ];
    }
}
