<?php

namespace App\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDailyTimeRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'records' => ['required', 'array', 'min:1'],
            'records.*.employee_id' => ['required', 'exists:employees,id'],
            'records.*.date' => ['required', 'date'],
            'records.*.cutoff' => ['required', Rule::in(['First Half', 'Second Half', 'Custom'])],
            'records.*.period_from' => ['required', 'date'],
            'records.*.period_to' => ['required', 'date'],
            'records.*.day_type' => ['required', Rule::in([
                'Regular Working Day',
                'Rest Day',
                'Regular Holiday',
                'Regular Holiday on Rest Day',
                'Special Non-Working Day',
                'Special Non-Working Day on Rest Day',
            ])],
            'records.*.attendance' => ['required', Rule::in(['Present', 'Absent', 'Leave'])],
            'records.*.leave_type' => ['nullable', Rule::in(['Vacation', 'Sick', 'Emergency', 'Other', 'Unpaid Leave'])],
            'records.*.overtime_hours' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
