<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_name' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'available_stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'reserved_stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'reorder_point' => ['sometimes', 'required', 'integer', 'min:0'],
            'warehouse' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
