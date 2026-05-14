<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $layup = $this->route('layup');
        $layupId = is_object($layup) ? $layup->id : $layup;

        return [
            'layer_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('clt_layers', 'layer_order')->where(fn ($query) => $query->where('layup_id', $layupId)),
            ],
            'thickness' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'angle' => ['required', 'numeric', 'between:-360,360'],
        ];
    }
}
