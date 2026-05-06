<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'string', 'max:100', 'unique:employees,employee_id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'date_hired' => ['required', 'date'],
            'department' => ['required', 'string', 'max:255'],
            'employment_status' => ['required', Rule::in(['Probationary', 'Regular', 'Intern'])],
            'salary_type' => ['required', Rule::in(['Monthly', 'Daily'])],
            'base_salary_encrypted' => ['required', 'numeric', 'min:0'],
            'daily_rate' => ['nullable', 'numeric', 'min:0'],
            'sss_contribution' => ['nullable', 'numeric', 'min:0'],
            'philhealth_contribution' => ['nullable', 'numeric', 'min:0'],
            'pagibig_contribution' => ['nullable', 'numeric', 'min:0'],
            'withholding_tax' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
