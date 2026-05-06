<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_name' => ['sometimes', 'required', 'string', 'max:255'],
            'project' => ['sometimes', 'required', 'string', 'max:255'],
            'stage' => ['sometimes', 'required', Rule::in(['Lead', 'Negotiation', 'Awarded', 'Collected'])],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'owner_name' => ['sometimes', 'required', 'string', 'max:255'],
            'close_date' => ['nullable', 'date'],
        ];
    }
}
