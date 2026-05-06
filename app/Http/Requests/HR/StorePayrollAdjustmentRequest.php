<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayrollAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'label' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'type' => ['required', Rule::in(['Earning', 'Contribution', 'Deduction'])],
            'frequency' => ['required', Rule::in(['One Time', 'Every Payroll'])],
            'payroll_count' => ['required', 'integer', 'min:1'],
        ];
    }
}
