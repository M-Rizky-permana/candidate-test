<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLayupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');
        $supplierId = is_object($supplier) ? $supplier->id : $supplier;
        $layup = $this->route('layup');
        $layupId = is_object($layup) ? $layup->id : $layup;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clt_layups', 'name')
                    ->where(fn ($query) => $query->where('supplier_id', $supplierId))
                    ->ignore($layupId),
            ],
        ];
    }
}
