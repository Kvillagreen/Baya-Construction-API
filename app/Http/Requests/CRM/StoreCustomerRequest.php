<?php

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::in(['pending', 'approved', 'cancelled', 'finished'])],
            'contact_person' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:80'],
            'project_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'purchase_order_number' => ['nullable', 'string', 'max:255'],
            'project_costing_amount_encrypted' => ['required', 'numeric', 'min:0'],
        ];
    }
}
