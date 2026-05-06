<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryTransactionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'item_id' => ['required', 'exists:inventory_items,id'],
            'transaction_type' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'performed_by' => ['nullable', 'exists:users,id'],
            'transaction_date' => ['nullable', 'date'],
        ];
    }
}
