<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLayupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');
        $supplierId = is_object($supplier) ? $supplier->id : $supplier;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clt_layups', 'name')->where(fn ($query) => $query->where('supplier_id', $supplierId)),
            ],
        ];
    }
}
