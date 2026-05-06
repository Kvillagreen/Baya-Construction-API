<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_name' => ['required', 'string', 'max:255'],
            'project' => ['required', 'string', 'max:255'],
            'stage' => ['required', Rule::in(['Lead', 'Negotiation', 'Awarded', 'Collected'])],
            'amount' => ['required', 'numeric', 'min:0'],
            'owner_name' => ['required', 'string', 'max:255'],
            'close_date' => ['nullable', 'date'],
        ];
    }
}
