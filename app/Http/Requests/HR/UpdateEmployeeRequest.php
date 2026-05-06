<?php

namespace App\Http\Requests\HR;

use App\Models\HR\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        $employeeId = $employee instanceof Employee ? $employee->getKey() : (string) $employee;

        return [
            'employee_id' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('employees', 'employee_id')->ignore($employeeId)],
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
            'date_hired' => ['sometimes', 'required', 'date'],
            'department' => ['sometimes', 'required', 'string', 'max:255'],
            'employment_status' => ['sometimes', 'required', Rule::in(['Probationary', 'Regular', 'Intern'])],
            'salary_type' => ['sometimes', 'required', Rule::in(['Monthly', 'Daily'])],
            'base_salary_encrypted' => ['sometimes', 'required', 'numeric', 'min:0'],
            'daily_rate' => ['nullable', 'numeric', 'min:0'],
            'sss_contribution' => ['nullable', 'numeric', 'min:0'],
            'philhealth_contribution' => ['nullable', 'numeric', 'min:0'],
            'pagibig_contribution' => ['nullable', 'numeric', 'min:0'],
            'withholding_tax' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
