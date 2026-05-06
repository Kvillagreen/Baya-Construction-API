<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessSaleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer' => ['nullable', 'array'],
            'customer.company_name' => ['required_without:customer_id', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'project' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:255'],
            'stage' => ['required', 'string', 'max:255'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'close_date' => ['nullable', 'date'],
            'expected_close_date' => ['nullable', 'date'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'project_details' => ['nullable', 'array'],
        ];
    }
}
